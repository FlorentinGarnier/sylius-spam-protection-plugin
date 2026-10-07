@spam_protection_registration
Feature: Protecting the registration form
    In order to keep fake accounts out of my store
    As a Store Owner
    I want the registration form to reject the accounts created by bots

    Background:
        Given the store operates on a single channel in "United States"

    @javascript
    Scenario: Registering from a browser, while the form validates the fields as they are filled in
        When I want to register a new account
        And I specify the first name as "Saul"
        And I specify the last name as "Goodman"
        And I specify the email as "goodman@gmail.com"
        And I take as long as a human to fill in the form
        And I specify the password as "heisenberg"
        And I confirm this password
        And I register this account
        And I wait for my browser to solve the proof of work and send the form
        Then I should be notified that new account has been successfully created

    Scenario: Rejecting a registration sent without solving the proof of work
        When I want to register a new account
        And I specify the first name as "Saul"
        And I specify the last name as "Goodman"
        And I specify the email as "goodman@gmail.com"
        And I specify the password as "heisenberg"
        And I confirm this password
        And I take as long as a human to fill in the form
        And I try to register this account
        Then I should be notified that my request could not be sent
