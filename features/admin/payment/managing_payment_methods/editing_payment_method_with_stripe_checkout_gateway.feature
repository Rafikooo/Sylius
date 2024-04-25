@managing_payment_methods
Feature: Editing payment method configured with a Stripe Checkout gateway
    In order to change which payment methods are available in my store
    As an Administrator
    I want to be able to edit payment method

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a payment method "Stripe Checkout" with a code "stripe" and "Stripe Checkout" gateway
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Changing Stripe Checkout gateway publishable key
        When I want to modify the "Stripe Checkout" payment method
        And I update its "Publishable key" field with "some_publishable_key"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this payment method "Publishable key" should be "some_publishable_key"

    @ui @no-api
    Scenario: Changing Stripe Checkout gateway secret key
        When I want to modify the "Stripe Checkout" payment method
        And I update its "Secret key" field with "some_secret_key"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this payment method "Secret key" should be "some_secret_key"
