@spam_protection_contact
Feature: Protecting the contact form
    In order to receive only genuine contact requests
    As a Store Owner
    I want the contact form to reject the requests sent by bots

    Background:
        Given the store operates on a single channel in "United States"
        And this channel has contact email set as "contact@goodshop.com"

    @javascript @email
    Scenario: Sending a contact request from a browser
        When I want to request contact
        And I specify the email as "lucifer@morningstar.com"
        And I specify the message as "Hi! I did not receive an item!"
        And I take as long as a human to fill in the form
        And I send it
        And I wait for my browser to solve the proof of work and send the form
        Then I should be notified that the contact request has been submitted successfully
        And the email with contact request should be sent to "contact@goodshop.com"

    @email
    Scenario: Rejecting a contact request sent without solving the proof of work
        When I want to request contact
        And I specify the email as "lucifer@morningstar.com"
        And I specify the message as "Hi! I did not receive an item!"
        And I take as long as a human to fill in the form
        And I try to send it
        Then I should be notified that my request could not be sent
        And "contact@goodshop.com" should receive no emails
