# Tournament Processor (PHP)

PHP-based tournament data processor supporting CLI and browser execution, designed to read complex JSON input, validate match data, compute standings, and return structured results.

## Overview

This project processes tournament match data from a JSON input file and generates a structured summary including:

- Team standings
- Top scoring team
- Total matches played

The solution includes robust validation, error handling, and flexible testing approaches.

## Features

- JSON input parsing and validation
- Tournament standings computation
- Multi-level sorting:
  1. Points
  2. Goal Difference
  3. Goals Scored
  4. Team Name
- Error handling using exceptions
- Supports execution via:
  - CLI (Command Line)
  - Browser (via query parameters)
- Flexible testing:
  - Individual test case files
  - Combined test file with key-based selection

## Design Principles

The implementation follows:

- **Single Responsibility Principle**  
- **Separation of Concerns**  
- Modular function-based structure  
- Clean and maintainable code design  

Each function handles a specific responsibility such as input handling, validation, processing, sorting, or output.

## Project Structure

    ├── index.php
    ├── input.json
    ├── README.md
    │
    ├── test-cases/
    │ ├── valid.json
    │ ├── invalid_missing_team.json
    │ ├── invalid_score_type.json
    │ ├── invalid_negative_score.json
    │ ├── invalid_same_team.json
    │ ├── invalid_empty_matches.json
    │ └── all_tests.json
    │
    └── output/
    ├── valid_output.json
    ├── invalid_missing_team_output.json
    ├── invalid_negative_score_output.json

## How to Run

### 🔹 1. Using CLI (Recommended)
    Run default input:
        php index.php

    Run specific test case:
        php index.php test-cases/invalid_missing_team.json
    
    Run combined test file with key:
        php index.php test-cases/all_tests.json valid_case
        php index.php test-cases/all_tests.json invalid_negative_score

### 🔹 2. Using Browser
    Default:
        http://localhost:8003/tournament-processor-php/index.php

    With file:
        http://localhost:8003/tournament-processor-php/index.php?file=test-cases/invalid_missing_team.json

    With combined test + key:
        http://localhost:8003/tournament-processor-php/index.php?file=test-cases/all_tests.json&key=invalid_negative_score


## Input Format

The input JSON must include:

- 'teams': list of teams with unique IDs
- 'matches': list of matches

### Example:

json:
{
  "teams": [
    { "id": 1, "name": "Dragons" },
    { "id": 2, "name": "Wolves" }
  ],
  "matches": [
    {
      "id": 101,
      "home_team": 1,
      "away_team": 2,
      "home_score": 2,
      "away_score": 1
    }
  ]
}

## Validations Implemented

The system validates:
    - Team IDs must exist
    - Match teams must be different
    - Scores must be integers
    - Scores cannot be negative
    - Matches list cannot be empty

If validation fails, an error is returned:
{
  "error": "Invalid team ID in match"
}

## Test Cases

### Success Cases
    - input.json
    - test-cases/valid.json
### Failure Cases
    - invalid_missing_team.json
    - invalid_score_type.json
    - invalid_negative_score.json
    - invalid_same_team.json
    - invalid_empty_matches.json
### Combined Testing
    - test-cases/all_tests.json
    - Supports switching via key parameter

---

## Output Samples

Sample outputs are available in the output/ folder for both valid and invalid cases.

Examples:
    - output/valid_output.json - Successful execution
    - output/invalid_missing_team_output.json - Validation error

These outputs can be reproduced by running corresponding test cases.

## Notes & Decisions
    - Implemented both:
        - Standard test case files (recommended approach)
        - Combined test file with key-based selection (for flexibility)
    - CLI and browser support added for flexible testing and automation
    - Functions are modular to improve readability and maintainability
    - Exceptions used for clean error handling