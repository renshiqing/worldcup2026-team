<?php
$pdo = new PDO(
    'mysql:host=127.0.0.1;port=3307;dbname=worldcup2026_team;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$teams = [
    1 => ['德国队', '德国'],
    2 => ['法国队', '法国'],
    3 => ['美国队', '美国'],
    4 => ['日本队', '日本'],
    5 => ['巴西队', '巴西'],
    6 => ['阿根廷队', '阿根廷'],
    7 => ['英格兰队', '英格兰'],
    8 => ['西班牙队', '西班牙'],
];

$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare(
        'UPDATE football_team
         SET name = :name, country = :country
         WHERE id = :id'
    );

    foreach ($teams as $id => $team) {
        $stmt->execute([
            ':id' => $id,
            ':name' => $team[0],
            ':country' => $team[1],
        ]);
    }

    $pdo->commit();
    echo "球队信息修改成功！" . PHP_EOL;

} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}

foreach ($pdo->query(
    'SELECT id, name, country, group_id
     FROM football_team ORDER BY id'
) as $row) {
    echo $row['id'] . ' | '
       . $row['name'] . ' | '
       . $row['country'] . ' | '
       . $row['group_id'] . PHP_EOL;
}
