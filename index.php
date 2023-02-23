<?php 
// Initalize empty board
$board = array(array('?', '?', '?'), array('?', '?', '?'), array('?', '?', '?'));
// Gloabal string for query_string
$query = "";
// Game state global variables
$done = false;
$winningMessage = "";

// Check if new game
if (empty($_SERVER['QUERY_STRING']) && empty($_SERVER['HTTP_REFERER']))
{
    $query = "";
    ComputerMove();
}

// Check if user tried editing uri
elseif (!empty($_SERVER['QUERY_STRING']) && empty($_SERVER['HTTP_REFERER']))
{
    // Keep the hackers out
    die("No Editing the URI\n");
}

// Continued game (legal)
else 
{
    $query = $_SERVER['QUERY_STRING'];

    if (strlen($query) % 2 == 1)
    {
        //Incase an odd number of digits appears in the query string
        die("URI doesn't hold valid information");
    }
    SetBoard($query);

    // Check if game is finished
    if (CheckForWin())
    {
        $GLOBALS['done'] = true;
    }
    else
    {
        ComputerMove();
    }

    // Check for winner after Computer Move
    if (CheckForWin())
    {
        $GLOBALS['done'] = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tic Tac Toe</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="TicTacToe-Game">
    <meta name="author" content="Matthew Gallagher">
</head>
<body style="height: 90vh;">  

    <!--    This section displays the Game Board while playing  -->

    <div style="display: flex; flex-direction: column; align-items: center; box-sizing: border-box;">
        <h1>Tic Tac Toe</h1>
        <?php if ($GLOBALS['done'] == true) { ?>
        <div style="display: flex; flex-direction: column; align-items: center; box-sizing: border-box;">
            <h1><?php echo $GLOBALS['winningMessage']; ?></h1>
            <a href="/tictactoe1">Play Again!</a>
        </div>
        <?php } ?>
        <table style="margin: 25px;">
            <?php for ($i = 0; $i < 3; ++$i) { ?>
            <tr>
                <?php for ($j = 0; $j < 3; ++$j) { ?>
                <td style="padding-inline: 15px;">
                    <?php if (CheckIfOpen($i, $j) && $GLOBALS['done'] == false) {?>
                    <a href=<?php echo '?'.$GLOBALS['query'].$i.$j; ?>><?php echo $GLOBALS['board'][$i][$j]; ?> </a>
                    <?php } elseif ((CheckIfOpen($i, $j) == null || $GLOBALS['done'] == true)) { ?>
                    <p><?php echo $GLOBALS['board'][$i][$j] ?></p>
                    <?php } ?>
                </td>
                <?php } ?>
            </tr>
            <?php } ?>
        </table>
        <h4>Win the game by scoring three in a row, column, or diagonal before your opponent does</h4>
        
    </div>
</body>



<?php 
/* SECTION FOR FUNCTIONS */
function SetBoard($params)
{
    // Even is computer's move
    // Odd is user's move
    for ($iter = 0; $iter < strlen($params)/2; ++$iter)
    {
        $row = substr($params, ($iter*2), 1);
        $col = substr($params, (($iter*2) + 1), 1);
        // Computer Turn
        if (($iter == 0) || ($iter % 2 == 0))
        {
            UpdateBoard($row, $col, 'X');
        }
        // User Turn
        else if ($iter % 2 == 1)
        {
            UpdateBoard($row, $col, 'O');
        }
    }
}

/* Function to generate available computer move */
function ComputerMove() 
{
    $found = null;
    while (!isset($found))
    {
        $row = rand(0,2);
        $col = rand(0,2);
        if (CheckIfOpen($row, $col))
        {
            UpdateBoard($row, $col, 'X');
            $GLOBALS['query'] .= $row.$col;
            $found = true;
        }
    }
}

/* Function to update Global variable $board with a char */
function UpdateBoard($row, $col, $char)
{
    $GLOBALS['board'][$row][$col] = $char;
}

/* Function to check if there is an available space */
/* Used mostly in html to limit user error  */
/* I turned them into a <p> tag instead of an <a> tag */
function CheckIfOpen($row, $col) 
{
    if ($GLOBALS['board'][$row][$col] == '?') 
    {
        return true;
    }
    return false;
}


/* Function to check if there is a winner  */
/* If there is a winner, the function will return 'X' or 'O'  */
function CheckLinesAndWinner()
{
    $board = $GLOBALS['board'];
    foreach ($board as $value)
    {
        if ((($value[0] == $value[1]) && ($value[1] == $value[2])) && ($value[0] != '?')) { return $value[0]; }
    }
    for ($i = 0; $i < 3; ++$i)
    {
        if ((($board[0][$i] == $board[1][$i]) && ($board[1][$i] == $board[2][$i])) && ($board[0][$i] != '?')) { return $board[0][$i]; }
    }
    if ((($board[0][0] == $board[1][1]) && ($board[1][1] == $board[2][2])) && ($board[0][0] != '?')) { return $board[0][0]; }
    if ((($board[2][0] == $board[1][1]) && ($board[1][1] == $board[0][2])) && ($board[2][0] != '?')) { return $board[2][0]; }
    return "";
}

/* Function to determine the winner based on the output of CheckLinesAndWinner()  */
/* Only applicable for determine a win, not a tie   */
function CheckForWin()
{
    $winner = CheckLinesAndWinner();
    $query = $GLOBALS['query'];
    if (strlen($query) >= 18) 
    {
        $GLOBALS['winningMessage'] = "The Game Is A Tie.";
        return true;
    }
    elseif ($winner == 'X')
    {
        $GLOBALS['winningMessage'] = "The Computer Wins.";
        return true;
    }
    elseif ($winner == 'O')
    {
        $GLOBALS['winningMessage'] = "You Won!!";
        return true;
    }
    else { return false; }
}
?>