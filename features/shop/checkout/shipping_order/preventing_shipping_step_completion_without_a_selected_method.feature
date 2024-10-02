@checkout
Feature: Prevent shipping step completion without a selected shipping method
    In order to prevent finishing the shipping step without a selected method
    As a Customer
    I want to be prevented from completing the shipping step without selecting a shipping method

    Background:
        Given I am a logged in customer

    @api @no-ui
    Scenario: Preventing shipping step completion if there are no available shipping methods
        Given the store operates on a single channel in "United States"
        Given the store has a product "PHP T-Shirt"
        And I have the product "PHP T-Shirt" in the cart
        And I have addressed the cart to "United States"
        When I check the details of my cart
        Then I should see that there is no assigned shipping method
        And there should not be any shipping method available to choose

    @no-api @ui @javascript
    Scenario: Preventing shipping step completion if there are no available shipping methods
        Given the store operates on a single channel in "United States"
        Given the store has a product "PHP T-Shirt"
        And I have the product "PHP T-Shirt" in the cart
        And I have addressed the cart to "United States"
        When I try to complete the shipping step
        Then I should be informed that my order cannot be shipped to this address
        And I should not be able to proceed checkout shipping step

    @api @no-ui
    Scenario: Preventing shipping step completion if there are no available shipping methods for selected country
        Given the store operates on a channel named "Clothes"
        And this store operates in "France"
        And this store operates in "United States"
        And the store has a product "T-Shirt"
        And the store has a zone "Europe" with code "EU"
        And this zone has the "France" country member
        And the store has "DHL" shipping method with "$20.00" fee within the "EU" zone
        And I have product "T-Shirt" in the cart
        And I have addressed the cart to "United States"
        When I check the details of my cart
        And I try to select "DHL" shipping method
        Then I should see that this shipping method is not available for this address

    @no-api @ui @javascript
    Scenario: Preventing shipping step completion if there are no available shipping methods for selected country
        Given the store operates on a channel named "Clothes"
        And this store operates in "France"
        And this store operates in "United States"
        And the store has a product "T-Shirt"
        And the store has a zone "Europe" with code "EU"
        And this zone has the "France" country member
        And the store has "DHL" shipping method with "$20.00" fee within the "EU" zone
        And I have product "T-Shirt" in the cart
        And I have addressed the cart to "United States"
        When I try to complete the shipping step
        Then I should be informed that my order cannot be shipped to this address
        And I should not be able to proceed checkout shipping step

