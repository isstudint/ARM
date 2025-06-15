
SELECT team_id, team_name FROM teams ORDER BY team_id;

-- Clean slate
DELETE FROM scores;
DELETE FROM matches;

-- FIXED: Each team plays exactly 3 games (12 total matches for 8 teams)
INSERT INTO matches (team1_id, team2_id, match_date, status)
SELECT 
    t1.team_id,
    t2.team_id,
    CASE 
        WHEN ROW_NUMBER() OVER (ORDER BY t1.team_id, t2.team_id) <= 4 THEN '2025-01-20 14:00:00'
        WHEN ROW_NUMBER() OVER (ORDER BY t1.team_id, t2.team_id) <= 8 THEN '2025-01-21 14:00:00'
        ELSE '2025-01-22 14:00:00'
    END as match_date,
    'Completed'
FROM (
    SELECT team_id, ROW_NUMBER() OVER (ORDER BY team_id) as rn FROM teams
) t1
INNER JOIN (
    SELECT team_id, ROW_NUMBER() OVER (ORDER BY team_id) as rn FROM teams
) t2 ON t1.rn < t2.rn
WHERE (
    (t1.rn = 1 AND t2.rn = 2) OR
    (t1.rn = 3 AND t2.rn = 4) OR
    (t1.rn = 5 AND t2.rn = 6) OR
    (t1.rn = 7 AND t2.rn = 8) OR
    (t1.rn = 1 AND t2.rn = 3) OR
    (t1.rn = 2 AND t2.rn = 4) OR
    (t1.rn = 5 AND t2.rn = 7) OR
    (t1.rn = 6 AND t2.rn = 8) OR
    (t1.rn = 1 AND t2.rn = 5) OR
    (t1.rn = 2 AND t2.rn = 6) OR
    (t1.rn = 3 AND t2.rn = 7) OR
    (t1.rn = 4 AND t2.rn = 8)
);






INSERT INTO scores (match_id, team1_score, team2_score) 
SELECT m.match_id, 
    FLOOR(RAND() * 30) + 60,  -- Random score 60-89
    FLOOR(RAND() * 30) + 60   -- Random score 60-89
FROM matches m 
LEFT JOIN scores s ON m.match_id = s.match_id 
WHERE s.match_id IS NULL;


UPDATE matches m 
JOIN scores s ON m.match_id = s.match_id 
SET m.status = 'Completed';


SELECT 
    t.team_name,
    COUNT(m.match_id) as games_played
FROM teams t
LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
GROUP BY t.team_id, t.team_name
ORDER BY t.team_name;









