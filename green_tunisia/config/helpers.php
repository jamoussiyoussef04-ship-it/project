<?php

function e($valeur): string
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}


function url(string $controller, string $action, array $params = []): string
{
    return 'index.php?' . http_build_query(array_merge(['controller' => $controller, 'action' => $action], $params));
}
