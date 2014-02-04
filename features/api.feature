Feature: API Test

  Scenario: Test API Test :)
    When I send a GET request to "/api/test"
    Then the response code should be 200
    And the response should contain "test"

  Scenario: Send PNG Image
    Given I set header "Content-Type" with value "image/png"
    When I send a POST request to "/api/image" with raw file body:
    """
      hRoCX3Jpe96eggiZ4NqMJA.png
    """
    #Then print response
    Then the response code should be 201


  Scenario: Send PNG Image with wrong Content-Type
    Given I set header "Content-Type" with value "image/bla"
    When I send a POST request to "/api/image" with raw file body:
    """
      hRoCX3Jpe96eggiZ4NqMJA.png
    """
    #Then print response
    Then the response code should be 500

  Scenario: Send Fake Image with fake Content-Type
    Given I set header "Content-Type" with value "image/png"
    When I send a POST request to "/api/image" with raw file body:
    """
      api.feature
    """
    #Then print response
    Then the response code should be 500
