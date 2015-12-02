Feature: JWT verification
  Test for JWT extension and verification headers

  Scenario: Request Without JWT token
    Given I set header "Content-Type" with value "application/json"
    And I send a GET request to "/api/protected_resource"
    Then the response code should be 401

  Scenario: Request With JWT token
    Given I set header "Content-Type" with value "application/json"
    And I am authenticating with jwt token as "admin"
    When I send a GET request to "/api/protected_resource"
    Then the response code should be 200
    And response should contain json:
    """
    {
     "hello":"admin",
     "username":"admin",
     "auth":"yes",
     "granted":"yes",
     "granted_user":"no",
     "granted_super":"yes"
    }
    """

  Scenario: Validate jwt token
    Given I set header "Content-Type" with value "application/json"
    When I send a POST request to "/api/login" with body:
    """
      {
        "_username":"admin",
        "_password":"foo"
      }
    """
    Then the response code should be 200
    And response should contain jwt token in field "token"
    And response should contain jwt token in field "token" with data:
    """
        {
          "name":"admin"
        }
    """



