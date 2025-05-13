<?php
session_start();

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'quiz';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$questions = [
    1 => ['question' => '¿que fruta es verde por fuera y roja por dentro?', 'answers' => ['a' => 'papaya', 'b' => 'sandia', 'c' => 'manzana', 'd' => 'uva'], 'correct' => 'b'],
    2 => ['question' => '¿que materia estas viendo?', 'answers' => ['a' => 'arquitectura de software', 'b' => 'creencias cristianas ', 'c' => 'etica', 'd' => 'religión'], 'correct' => 'a']
];

$message = '';
$score = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
    $student_name = htmlspecialchars($_POST['student_name']);
    
    foreach ($_POST as $question => $answer) {
        if (isset($questions[intval($question)])){
            if ($questions[intval($question)]['correct'] === $answer) {
                $score++;
            }
        }
    }
    
    $percentage = ($score / count($questions)) * 100;
    $stmt = $conn->prepare("INSERT INTO resultados (nombre_estudiante, puntaje, porcentaje, fecha) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sid", $student_name, $score, $percentage);
    
    if ($stmt->execute()) {
        $message = "<div class='success'>¡Tus respuestas han sido guardadas, $student_name! Has obtenido $score de " . count($questions) . " preguntas correctas ($percentage%).</div>";
    } else {
        $message = "<div class='error'>Hubo un error al guardar tus resultados. Por favor intenta nuevamente.</div>";
    }
    
    $stmt->close();
}

$conn->close();
?>
    
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz </title>
    <link rel="stylesheet" href="quiz-styles.css">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .question {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .answers {
            margin-top: 10px;
        }
        .answer {
            margin: 5px 0;
        }
        .success {
            color: green;
            padding: 10px;
            background-color: #e6ffe6;
            border: 1px solid green;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error {
            color: red;
            padding: 10px;
            background-color: #ffe6e6;
            border: 1px solid red;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Quiz</h1>
        
        <?php echo $message; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="student_name">Tu nombre:</label>
                <input type="text" id="student_name" name="student_name" required>
            </div>

            <?php foreach ($questions as $id => $q): ?>
                <div class="question">
                    <p><?php echo $id; ?>. <?php echo $q['question']; ?></p>
                    <div class="answers">
                        <?php foreach ($q['answers'] as $letter => $answer): ?>
                            <div class="answer">
                                <input type="radio" id="q<?php echo $id; ?>_<?php echo $letter; ?>" name="<?php echo $id; ?>" value="<?php echo $letter; ?>" required>
                                <label for="q<?php echo $id; ?>_<?php echo $letter; ?>"><?php echo $letter; ?>) <?php echo $answer; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <input type="submit" name="submit_quiz" value="Enviar respuestas">
        </form>
    </div>
</body>
</html>