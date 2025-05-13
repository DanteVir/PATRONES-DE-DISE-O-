<?php
/* PATRÓN SINGLETON (Creacional) asegura una única instancia de conexión a base de datos */ 
class DatabaseConnection {
    private static $instance;
    private $conn;
    private function __construct() {
        $this->conn = new mysqli('localhost', 'root', '', 'quiz');
        if ($this->conn->connect_error) die("Error de conexión: " . $this->conn->connect_error);
    }
    public static function getInstance() {
        if (!self::$instance) self::$instance = new self();
        return self::$instance->conn;
    }
}
interface ScoringStrategy {
    public function calculate($questions, $answers);
}
/* este sirve para definir el puntaje en el caso del quiz tiene algoritmos intercambiables  PATRÓN STRATEGY  */
class StandardScoring implements ScoringStrategy {
    public function calculate($questions, $answers) {
        $score = 0;
        foreach ($answers as $q => $a) {
            if (isset($questions[$q]) && $questions[$q]['correct'] === $a) $score++;
        }
        return $score;
    }
}

/* PATRÓN FACADE (Estructural) simplifica el proceso completo del quiz puntaje + guardado en la base de datos */
class QuizFacade {
    private $scoringStrategy;

    public function __construct(ScoringStrategy $strategy) {
        $this->scoringStrategy = $strategy;
    }

    public function processQuiz($questions, $postData) {
        $conn = DatabaseConnection::getInstance();
        $student_name = htmlspecialchars($postData['student_name']);
        $answers = array_filter($postData, fn($key) => is_numeric($key), ARRAY_FILTER_USE_KEY);

        $score = $this->scoringStrategy->calculate($questions, $answers);
        $percentage = ($score / count($questions)) * 100;

        $stmt = $conn->prepare("INSERT INTO resultados (nombre_estudiante, puntaje, porcentaje, fecha) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sid", $student_name, $score, $percentage);

        return $stmt->execute() ? 
            "<div class='success'>¡Resultado: $score/" . count($questions) . " ($percentage%)</div>" :
            "<div class='error'>Error al guardar</div>";
    }
}
$questions = [
    1 => [
        'question' => '¿Qué fruta es verde por fuera y roja por dentro?',
        'answers' => ['a' => 'papaya', 'b' => 'sandia', 'c' => 'manzana', 'd' => 'uva'],
        'correct' => 'b'
    ],
    2 => [
        'question' => '¿Qué materia estás viendo?',
        'answers' => ['a' => 'arquitectura de software', 'b' => 'creencias cristianas', 'c' => 'ética', 'd' => 'religión'],
        'correct' => 'a'
    ]
];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
    $facade = new QuizFacade(new StandardScoring());
    $message = $facade->processQuiz($questions, $_POST);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Quiz con Patrones</title>
    <style>
        .success { color: green; }
        .error { color: red; }
        .question { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Quiz</h1>
    <?= $message ?>
    <form method="post">
        <label>Nombre: <input type="text" name="student_name" required></label>
        <?php foreach ($questions as $id => $q): ?>
            <div class="question">
                <p><?= "$id. {$q['question']}" ?></p>
                <?php foreach ($q['answers'] as $letter => $answer): ?>
                    <label>
                        <input type="radio" name="<?= $id ?>" value="<?= $letter ?>" required>
                        <?= "$letter) $answer" ?>
                    </label><br>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit" name="submit_quiz">Enviar</button>
    </form>
</body>
</html>