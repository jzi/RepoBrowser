Feature: Listing repositories
  Background:
    Given the following repositories exist
      | name       | organization_name | creation_date | trust_score |
      | test-repo1 | test-org1         | 1970-01-01    |  0          |
      | test-repo2 | test-org1         | 1980-01-01    |  5.1        |
      | test-repo3 | test-org2         | 1990-01-01    |  10.2       |
  Scenario: I want to list known repositories
    When I request "/api/repositories.json" using HTTP "GET"
    Then the response code is "200"
    And  the response body is a JSON array of length "3"
    And  the response contains the repository "test-repo1" from organization "test-org1"
    And  the response contains the repository "test-repo2" from organization "test-org1"
    And  the response contains the repository "test-repo3" from organization "test-org2"