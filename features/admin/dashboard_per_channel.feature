@admin_dashboard
Feature: Statistics dashboard per channel
    In order to have an overview of my sales
    As an Administrator
    I want to see overall statistics on my admin dashboard in a specific channel

    Background:
        Given the store operates on a channel named "WEB-POLAND"
        And there is product "Onion" available in this channel
        And the store operates on another channel named "WEB-US"
        And there is product "Banana" available in that channel
        And the store ships everywhere for Free
        And the store allows paying Offline
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Seeing basic statistics for the first channel by default
        Given 3 customers have fulfilled 4 orders placed for total of "$8,566.00" mostly "Onion" product
        And 2 more customers have fulfilled 2 orders placed for total of "$459.00" mostly "Banana" product
        When I browse administration dashboard statistics
        Then I should see 4 new orders
        And I should see 5 new customers
        And there should be total sales of "$8,566.00"
        And the average order value should be "$2,141.50"

    @api @ui
    Scenario: Switching to the channel with only fulfilled orders
        Given 4 customers have fulfilled 4 orders placed for total of "$5,241.00" mostly "Onion" product
        And 2 more customers have fulfilled 2 orders placed for total of "$459.00" mostly "Banana" product
        And 2 more customers have placed 3 orders for total of "$1,259.00" mostly "Banana" product
        When I browse administration dashboard statistics for "WEB-POLAND" channel
        And I choose "WEB-US" channel
        Then I should see 2 new orders
        And I should see 8 new customers
        And there should be total sales of "$459.00"
        And the average order value should be "$229.50"

    @api @ui
    Scenario: Switching to the channel with both fulfilled and placed orders
        Given 4 customers have fulfilled 4 orders placed for total of "$5,241.00" mostly "Onion" product
        And 2 more customers have fulfilled 2 orders placed for total of "$459.00" mostly "Banana" product
        And 2 more customers have placed 3 orders for total of "$1,259.00" mostly "Banana" product
        When I browse administration dashboard statistics for "WEB-US" channel
        And I choose "WEB-POLAND" channel
        Then I should see 4 new orders
        And I should see 8 new customers
        And there should be total sales of "$5,241.00"
        And the average order value should be "$1,310.25"

    @api @ui
    Scenario: Seeing recent orders in a specific channel
        Given 3 customers have placed 4 orders for total of "$8,566.00" mostly "Onion" product
        And 2 more customers have placed 2 orders for total of "$459.00" mostly "Banana" product
        When I browse administration dashboard statistics for "WEB-POLAND" channel
        Then I should see 4 new orders in the list

    @api @ui
    Scenario: Seeing recent orders in a specific channel
        Given 3 customers have placed 4 orders for total of "$8,566.00" mostly "Onion" product
        And 2 more customers have placed 2 orders for total of "$459.00" mostly "Banana" product
        When I browse administration dashboard statistics for "WEB-US" channel
        Then I should see 2 new orders in the list
