<?php

function generateFilename($title, $author) { // Функція для генерації назви файлу
    $timestamp = date("YmdHis"); // Поточна дата та час у форматі року, місяця, дня, години, хвилини та секунд
    $filename = strtolower($title) . "-" . strtolower($author) . "-" . $timestamp . ".md"; // Генеруємо назву файлу
    return $filename; // Повертаємо назву файлу
}

function generateBlogPost($title, $author, $categories, $date, $content) { // Функція для генерації шаблону блог-посту
    $categoriesString = (count($categories) > 1) ? "[" . implode(", ", $categories) . "]" : $categories[0]; // Якщо категорія одна, то зберігається як рядок, інакше як масив
    $template = "---\n"; // Початок шаблону блог-посту
    $template .= "title: \"$title\"\n"; // Додаємо заголовок блог-посту
    $template .= "author: \"$author\"\n"; // Додаємо ім'я автора
    $template .= "category: $categoriesString\n";   // Додаємо категорію
    $template .= "date: \"$date\"\n"; // Додаємо дату
    $template .= "---\n\n"; // Кінець шаблону блог-посту
    $template .= "Вміст блогу:\n\n"; // Відокремлення між метаданими та вмістом блог-посту
    $template .= $content; // Вміст блог-посту
    return $template; // Повертаємо шаблон блог-посту
}

// Перевірка введених даних
function validateInput($input) {
    $trimmedInput = trim($input); // Видаляємо зайві пробіли
    if (empty($trimmedInput)) { // Перевіряємо, чи рядок не порожній
        echo "Поле не може бути порожнім.\n"; // Виводимо повідомлення про помилку
        return false;
    }
    if (mb_strlen($trimmedInput) < 3) { // Перевіряємо, чи довжина рядка не менше 3 символів
        echo "Довжина рядка має бути не менше 3 символів.\n"; // Виводимо повідомлення про помилку
        return false;
    }
    return true;
}

// Функція для повторного запиту даних у разі невалідного вводу
function promptInput($message) {
    do {  // Виконуємо цикл, поки введені дані не будуть валідними
        echo $message;  // Виводимо повідомлення
        $input = trim(fgets(STDIN)); // Зчитуємо введений рядок та видаляємо зайві пробіли
    } while (!validateInput($input)); // Повторюємо цикл, якщо введені дані невалідні
    return $input;
}

$title = promptInput("Введіть заголовок блог-посту: ");
$author = promptInput("Введіть ім'я автора: ");
$categoryInput = promptInput("Введіть категорії(є можливість введення декількох категорій через кому): ");
$categories = array_map('trim', explode(',', $categoryInput)); // Розділяємо введений рядок на масив категорій
echo "Введіть вміст блог-посту:\n";
$content = "";
while ($line = trim(fgets(STDIN))) {
    $content .= $line . "\n";
}

// Отримуємо поточний каталог
$currentDirectory = getcwd();
echo "Поточний каталог: $currentDirectory\n";

// Питаємо користувача, чи хоче він зберегти файл у поточному каталозі чи створити новий
echo "Введіть '1' якщо хочете зберегти файл у поточному каталозі, або '2', щоб створити новий: ";
$choice = trim(fgets(STDIN)); // Зчитуємо вибір користувача

if (strtolower($choice) === '1') { // Якщо користувач обрав збереження у поточному каталозі
    $outputDirectory = $currentDirectory; // Використовуємо поточний каталог
} else { // Якщо користувач обрав створення нового каталогу
    echo "Введіть ім'я нового каталогу: "; // Виводимо запит на введення імені нового каталогу
    $newDirectory = trim(fgets(STDIN)); // Зчитуємо введений користувачем рядок та видаляємо зайві пробіли

    // Створюємо новий каталог
    if (!file_exists($newDirectory)) { // Якщо каталог не існує
        mkdir($newDirectory, 0777, true); // Створюємо каталог
    }
    $outputDirectory = $newDirectory; // Використовуємо новий каталог
}

$currentDate = date("Y-m-d"); // Поточна дата у форматі року, місяця та дня

$filename = generateFilename($title, $author); // Генеруємо назву файлу
$template = generateBlogPost($title, $author, $categories, $currentDate, $content); // Генеруємо шаблон блог-посту
$filePath = $outputDirectory . "/" . $filename; // Формуємо шлях до файлу

file_put_contents($filePath, $template); // Записуємо шаблон блог-посту у файл

echo "Файл створено за шляхом: $filePath\n"; // Виводимо повідомлення про створення файлу
