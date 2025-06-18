SELECT t.team_id, t.team_name, t.logo,
       COUNT(m.match_id) AS total_matches,
       SUM((t.team_id = m.team1_id AND s.team1_score > s.team2_score) OR 
           (t.team_id = m.team2_id AND s.team2_score > s.team1_score)) AS wins,
       SUM((t.team_id = m.team1_id AND s.team1_score < s.team2_score) OR 
           (t.team_id = m.team2_id AND s.team2_score < s.team1_score)) AS losses,
       SUM(CASE WHEN t.team_id = m.team1_id THEN s.team1_score ELSE s.team2_score END) - 
       SUM(CASE WHEN t.team_id = m.team1_id THEN s.team2_score ELSE s.team1_score END) AS point_differential
FROM teams t
LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
LEFT JOIN scores s ON m.match_id = s.match_id
GROUP BY t.team_id, t.team_name, t.logo
ORDER BY wins DESC, point_differential DESC
LIMIT 8;

-- for fetching sa teams saka saka ang kanilang statistics eg. point differential, wins, losses, etc. 
-- ps. point differential formula is = points scored - points allowed 


SELECT p.player_id, p.player_name, p.position, p.jersey_num, p.image,
       ROUND(AVG(ps.points), 2) AS avg_points,
       ROUND(AVG(ps.rebounds), 2) AS avg_rebounds,
       ROUND(AVG(ps.assists), 2) AS avg_assists
FROM players p
LEFT JOIN player_stats ps ON p.player_id = ps.player_id
GROUP BY p.player_id
ORDER BY p.player_name;

-- para sa pagkuha ng mga players at kanilang statistics eg. average points, rebounds, assists, etc.



SELECT m.match_id, m.match_date, m.match_type, m.status,
       t1.team_name AS team1_name, t1.logo AS team1_logo,
       t2.team_name AS team2_name, t2.logo AS team2_logo,
       s.team1_score, s.team2_score
FROM matches m
INNER JOIN teams t1 ON m.team1_id = t1.team_id
INNER JOIN teams t2 ON m.team2_id = t2.team_id
LEFT JOIN scores s ON m.match_id = s.match_id
WHERE m.match_date >= CURDATE()
ORDER BY m.match_date ASC;

-- para sa pagkuha ng mga upcoming matches at kanilang mga teams at scores


SELECT m.match_id, m.match_date,
       t1.team_name AS team1_name, t1.logo AS team1_logo,
       t2.team_name AS team2_name, t2.logo AS team2_logo,
       s.team1_score, s.team2_score,
       CASE WHEN s.team1_score > s.team2_score THEN t1.team_name
            WHEN s.team2_score > s.team1_score THEN t2.team_name
       END AS winner_name
FROM matches m
JOIN teams t1 ON m.team1_id = t1.team_id
JOIN teams t2 ON m.team2_id = t2.team_id
JOIN scores s ON m.match_id = s.match_id
WHERE s.team1_score IS NOT NULL AND s.team2_score IS NOT NULL
ORDER BY m.match_date DESC;

-- para sa pagkuha ng mga past matches at kanilang mga teams, scores, at winners


SELECT t.team_id, t.team_name,
       COUNT(DISTINCT s.match_id) AS games_played,
       SUM((t.team_id = m.team1_id AND s.team1_score > s.team2_score) OR 
           (t.team_id = m.team2_id AND s.team2_score > s.team1_score)) AS wins
FROM teams t
LEFT JOIN matches m ON t.team_id = m.team1_id OR t.team_id = m.team2_id
LEFT JOIN scores s ON m.match_id = s.match_id
WHERE s.team1_score IS NOT NULL AND s.team2_score IS NOT NULL
GROUP BY t.team_id, t.team_name
HAVING games_played >= 3
ORDER BY wins DESC
LIMIT 4;

-- para sa pagkuha ng top 4 teams na may pinakamaraming wins 