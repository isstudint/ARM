mysqli_num_rows = return rows




Use px when you want the size to be fixed. Use rem/em when you want it to be adaptive to the scale of the system.

For example, fonts using px wont re-scale if you change font size of the system. If you use rem or em their size will change which adds accessability for people that have harder to read.



https://www.w3schools.com/php/func_array_in_array.asp


    // para to kasi user-friendly, di mawawala input ng user pag nag-error.
    diba kunwari nag input ka tas nag error mawawala yung input mo 




    https://www.youtube.com/watch?v=aq3InkyXmn0



    No match selected → Shows match list
Match clicked → URL gets ?match_id=X → Page reloads → Shows scoreboard for that match




https://codepen.io/semibran/pen/VjmPJd



INSERT INTO matches (team1_id, team2_id, match_date, status) VALUES

((SELECT team_id FROM teams WHERE team_name = 'Gentri'), (SELECT team_id FROM teams WHERE team_name = 'Tanza'), '2025-06-15 14:00:00', 'Scheduled'),
((SELECT team_id FROM teams WHERE team_name = 'Fontana'), (SELECT team_id FROM teams WHERE team_name = 'Paradahan'), '2025-06-16 15:00:00', 'Scheduled');


INSERT INTO scores (match_id, team1_score, team2_score) 
SELECT m.match_id, 
       FLOOR(RAND() * 30) + 60,  -- Random score 60-89
       FLOOR(RAND() * 30) + 60   -- Random score 60-89
FROM matches m 
LEFT JOIN scores s ON m.match_id = s.match_id 
WHERE s.match_id IS NULL
LIMIT 8;


UPDATE matches m 
JOIN scores s ON m.match_id = s.match_id 
SET m.status = 'Completed';


SELECT 
    t1.team_name AS Team1, 
    t2.team_name AS Team2, 
    m.match_date, 
    m.status,
    COALESCE(s.team1_score, 0) AS Score1,
    COALESCE(s.team2_score, 0) AS Score2
FROM matches m
JOIN teams t1 ON m.team1_id = t1.team_id
JOIN teams t2 ON m.team2_id = t2.team_id
LEFT JOIN scores s ON m.match_id = s.match_id
ORDER BY m.match_date;