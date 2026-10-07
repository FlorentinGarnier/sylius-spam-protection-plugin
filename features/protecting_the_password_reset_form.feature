@spam_protection_password_reset
Feature: Protecting the password reset form
    In order to keep my customers' mailboxes free of unrequested emails
    As a Store Owner
    I want the password reset form to reject the requests sent by bots

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "goodman@example.com" identified by "heisenberg"

    @javascript @email
    Scenario: Requesting a password reset from a browser
        When I want to reset password
        And I specify customer email as "goodman@example.com"
        And I take as long as a human to fill in the form
        And I reset it
        And I wait for my browser to solve the proof of work and send the form
        Then I should be notified that email with reset instruction has been sent
        And an email with reset token should be sent to "goodman@example.com"

    @email
    Scenario: Rejecting a password reset request sent without solving the proof of work
        When I want to reset password
        And I specify customer email as "goodman@example.com"
        And I take as long as a human to fill in the form
        And I reset it
        Then I should be notified that my request could not be sent
        And "goodman@example.com" should receive no emails
