@administrator_login
Feature: Redirecting on login page
    In order to access on my admin dashboard
    As a logged out Administrator
    I need to be redirected on login page

    Background:
        Given the store operates on a single channel in "United States"
        And I have been logged out from administration

    @ui @no-api
    Scenario: Redirecting on login page after being logout
        When I try to browse administration dashboard statistics
        Then I should be on login page
