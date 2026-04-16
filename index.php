<?php

function getInputParams()
{
    if (php_sapi_name() === 'cli') {
        global $argv;

        return [
            'file' => $argv[1] ?? "input.json",
            'key'  => $argv[2] ?? null
        ];
    }

    return [
        'file' => $_GET['file'] ?? "input.json",
        'key'  => $_GET['key'] ?? null
    ];
}

function readInput($file)
{
    if (!file_exists($file)) {
        throw new Exception("Input file not found");
    }

    $data = json_decode(file_get_contents($file), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON");
    }

    return $data;
}

function validateInput($data)
{
    if (!isset($data['teams']) || !isset($data['matches'])) {
        throw new Exception("Missing teams or matches");
    }

    if (empty($data['teams']) || empty($data['matches'])) {
        throw new Exception("Teams or matches cannot be empty");
    }
}

function initializeStandings($teams)
{
    $standings = [];

    foreach ($teams as $team) {
        $standings[$team['id']] = [
            'team' => $team['name'],
            'played' => 0,
            'wins' => 0,
            'draws' => 0,
            'losses' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'points' => 0
        ];
    }

    return $standings;
}

function validateMatch($match, $teamIds)
{
    if (!in_array($match['home_team'], $teamIds) ||
        !in_array($match['away_team'], $teamIds)) {
        throw new Exception("Invalid team ID in match");
    }

    if ($match['home_team'] === $match['away_team']) {
        throw new Exception("Same team cannot play against itself");
    }

    if (!is_int($match['home_score']) || !is_int($match['away_score'])) {
        throw new Exception("Scores must be integers");
    }

    if ($match['home_score'] < 0 || $match['away_score'] < 0) {
        throw new Exception("Scores cannot be negative");
    }
}

function processMatches($matches, &$standings, $teamIds)
{
    foreach ($matches as $match) {
        validateMatch($match, $teamIds);

        $home = &$standings[$match['home_team']];
        $away = &$standings[$match['away_team']];

        $home['played']++;
        $away['played']++;

        $home['goals_for'] += $match['home_score'];
        $home['goals_against'] += $match['away_score'];

        $away['goals_for'] += $match['away_score'];
        $away['goals_against'] += $match['home_score'];

        if ($match['home_score'] > $match['away_score']) {
            $home['wins']++;
            $home['points'] += 3;
            $away['losses']++;
        } elseif ($match['home_score'] < $match['away_score']) {
            $away['wins']++;
            $away['points'] += 3;
            $home['losses']++;
        } else {
            $home['draws']++;
            $away['draws']++;
            $home['points']++;
            $away['points']++;
        }
    }
}

function sortStandings(&$standings)
{
    usort($standings, function ($a, $b) {
        $gdA = $a['goals_for'] - $a['goals_against'];
        $gdB = $b['goals_for'] - $b['goals_against'];

        return
            $b['points'] <=> $a['points'] ?:
            $gdB <=> $gdA ?:
            $b['goals_for'] <=> $a['goals_for'] ?:
            strcmp($a['team'], $b['team']);
    });
}

function getTopScoringTeam($standings)
{
    $top = null;

    foreach ($standings as $team) {
        if ($top === null || $team['goals_for'] > $top['goals_for']) {
            $top = $team;
        }
    }

    return $top['team'];
}

function main()
{
    try {
        $params = getInputParams();

        $file = $params['file'];
        $key  = $params['key'];

        $rawData = readInput(__DIR__ . "/" . $file);

        // If key is provided then pick that test case
        if ($key !== null) {
            if (!isset($rawData[$key])) {
                throw new Exception("Test case not found: " . $key);
            }
            $data = $rawData[$key];
        } else {
            $data = $rawData;
        }

        validateInput($data);

        $teamIds = array_column($data['teams'], 'id');

        $standings = initializeStandings($data['teams']);

        processMatches($data['matches'], $standings, $teamIds);

        $standings = array_values($standings);

        sortStandings($standings);

        $result = [
            "standings" => $standings,
            "top_scoring_team" => getTopScoringTeam($standings),
            "total_matches" => count($data['matches'])
        ];

        echo json_encode($result, JSON_PRETTY_PRINT);

    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
}

main();