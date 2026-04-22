<?php

function getCurrentDateTimeString(): string
{
    $dt = new DateTime('now', new DateTimeZone('Europe/Madrid'));
    return $dt->format('d/m/Y H:i');
}

function getCurrentIsoDateTimeString(): string
{
    $dt = new DateTime('now', new DateTimeZone('Europe/Madrid'));
    return $dt->format(DATE_ATOM);
}

function sanitizeText(?string $value): string
{
    return trim((string) $value);
}

function calculateResult(array $factors, array $rawValues): array
{
    $totalScore = 0;
    $maxScore   = 0;

    foreach ($factors as $key => $data) {
        $value = isset($rawValues[$key]) ? (int) $rawValues[$key] : 3;
        $totalScore += $value * (int) $data['weight'];
        $maxScore   += 4 * (int) $data['weight'];
    }

    $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100) : 0;

    if ($percentage <= 40) {
        $risk = "Risc alt";
        $badge = "danger";
    } elseif ($percentage <= 70) {
        $risk = "Risc mitjà";
        $badge = "warning";
    } else {
        $risk = "Risc baix";
        $badge = "success";
    }

    return [
        "score" => $percentage,
        "risk" => $risk,
        "badge" => $badge,
        "total_score" => $totalScore,
        "max_score" => $maxScore
    ];
}

function getFactorSignal(int $value): array
{
    if ($value <= 1) {
        return ["label" => "Crític", "class" => "signal-red"];
    }

    if ($value === 2) {
        return ["label" => "Vigilància", "class" => "signal-yellow"];
    }

    return ["label" => "Favorable", "class" => "signal-green"];
}

function getFactorRows(array $factors, array $rawValues, array $rawNotes, array $rawJustifications, array $rawEvidence, int $totalWeight): array
{
    $rows = [];

    foreach ($factors as $key => $data) {
        $value  = isset($rawValues[$key]) ? (int) $rawValues[$key] : 3;
        $weight = (int) $data['weight'];
        $signal = getFactorSignal($value);

        $rows[] = [
            "key" => $key,
            "label" => $data['label'],
            "weight" => $weight,
            "weight_percent" => $totalWeight > 0 ? round(($weight / $totalWeight) * 100, 2) : 0,
            "selected_value" => $value,
            "selected_label" => $data['options'][$value] ?? '',
            "weighted_points" => $value * $weight,
            "max_weighted_points" => 4 * $weight,
            "note" => $rawNotes[$key] ?? '',
            "justification" => $rawJustifications[$key] ?? '',
            "evidence" => $rawEvidence[$key] ?? '',
            "signal" => $signal
        ];
    }

    return $rows;
}

function buildAlerts(array $values, float $globalScore): array
{
    $alerts = [];

    if (($values['tpm'] ?? 3) <= 1) {
        $alerts[] = [
            "level" => "danger",
            "title" => "Alerta crítica: mecanismes de protecció tècnica molt presents",
            "message" => "La presència habitual o obligatòria de mecanismes de protecció pot impedir còpies de preservació, migració i accés futur, fins i tot si la puntuació global no és baixa."
        ];
    }

    if (($values['dependencies'] ?? 3) <= 1) {
        $alerts[] = [
            "level" => "danger",
            "title" => "Alerta crítica: dependències externes elevades",
            "message" => "El format depèn massa d'un programari, sistema o entorn concret. Això incrementa molt el cost i el risc de preservació a llarg termini."
        ];
    }

    if (($values['disclosure'] ?? 3) <= 1) {
        $alerts[] = [
            "level" => "danger",
            "title" => "Alerta crítica: documentació insuficient",
            "message" => "Sense especificacions completes i eines de validació fiables, la preservació futura del format es complica de manera notable."
        ];
    }

    if (($values['transparency'] ?? 3) <= 1) {
        $alerts[] = [
            "level" => "warning",
            "title" => "Atenció: transparència baixa",
            "message" => "Una representació opaca o difícil d'analitzar complica la migració, la interpretació i la recuperació del contingut."
        ];
    }

    if (($values['selfdoc'] ?? 3) <= 1) {
        $alerts[] = [
            "level" => "warning",
            "title" => "Atenció: autodescripció insuficient",
            "message" => "La manca de metadades internes útils dificulta la gestió, la comprensió del context i la preservació del fitxer."
        ];
    }

    if (($values['patents'] ?? 3) <= 1) {
        $alerts[] = [
            "level" => "warning",
            "title" => "Atenció: possible impacte de patents o llicències",
            "message" => "Les patents o determinades condicions de llicència poden dificultar eines obertes, conversió i sostenibilitat futura."
        ];
    }

    if (($values['adoption'] ?? 3) <= 1) {
        $alerts[] = [
            "level" => "warning",
            "title" => "Atenció: adopció molt baixa",
            "message" => "Una adopció escassa sol implicar menys eines, menys compatibilitat i més risc d'obsolescència."
        ];
    }

    if ($globalScore >= 71 && ((($values['tpm'] ?? 3) <= 1) || (($values['dependencies'] ?? 3) <= 1) || (($values['disclosure'] ?? 3) <= 1))) {
        $alerts[] = [
            "level" => "info",
            "title" => "La mitjana global és bona, però hi ha factors crítics",
            "message" => "Una nota global favorable no anul·la riscos estructurals. Cal interpretar el resultat juntament amb les alertes específiques."
        ];
    }

    return $alerts;
}

function validateSubmission(array $factors, array $post): array
{
    $errors = [];

    foreach ($factors as $key => $data) {
        $value = isset($post[$key]) ? (int) $post[$key] : 3;

        if ($value < 0 || $value > 4) {
            $errors[$key] = "El valor seleccionat per a «{$data['label']}» no és vàlid.";
            continue;
        }

        $justification = sanitizeText($post[$key . '_justification'] ?? '');

        if ($value <= 1 && $justification === '') {
            $errors[$key . '_justification'] = "Has d'afegir una justificació quan marques 0 o 1 a «{$data['label']}».";
        }
    }

    return $errors;
}

function buildJsonPayload(array $meta, array $result, array $factorRows, array $alerts): array
{
    return [
        "metadades" => $meta,
        "resultat" => [
            "puntuacio_percentatge" => $result['score'],
            "valoracio_global" => $result['risk'],
            "puntuacio_bruta" => $result['total_score'],
            "puntuacio_maxima" => $result['max_score']
        ],
        "factors" => $factorRows,
        "alertes" => $alerts
    ];
}

function renderPdfHtml(array $meta, array $result, array $factorRows, array $alerts): string
{
    $styles = '
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1, h2, h3 { margin-bottom: 8px; }
        .meta p { margin: 4px 0; }
        .summary-box { padding: 10px; border: 1px solid #ccc; margin-bottom: 16px; }
        .alert { padding: 10px; border: 1px solid #ccc; margin-bottom: 8px; }
        .alert-danger { background: #fde2e1; }
        .alert-warning { background: #fff3cd; }
        .alert-info { background: #dbeafe; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 10px; table-layout: fixed; }
        th, td { border: 1px solid #ccc; padding: 5px; vertical-align: top; word-wrap: break-word; }
        th { background: #f2f2f2; }
        .small { font-size: 10px; color: #555; }
    ';

    $html = '<html><head><meta charset="utf-8"><style>' . $styles . '</style></head><body>';
    $html .= '<h1>Avaluació de factors de sostenibilitat</h1>';
    $html .= '<div class="meta">';
    $html .= '<p><strong>Format:</strong> ' . htmlspecialchars($meta['format']) . '</p>';
    $html .= '<p><strong>Versió:</strong> ' . htmlspecialchars($meta['version']) . '</p>';
    $html .= '<p><strong>Autor/a o estudiant:</strong> ' . htmlspecialchars($meta['student']) . '</p>';
    $html .= '<p><strong>Data d\'avaluació:</strong> ' . htmlspecialchars($meta['evaluation_date_human']) . '</p>';
    $html .= '</div>';
    $html .= '<div class="summary-box">';
    $html .= '<p><strong>Puntuació ponderada:</strong> ' . (int) $result['score'] . '%</p>';
    $html .= '<p><strong>Valoració global:</strong> ' . htmlspecialchars($result['risk']) . '</p>';
    $html .= '<p><strong>Puntuació bruta:</strong> ' . (int) $result['total_score'] . ' / ' . (int) $result['max_score'] . '</p>';
    $html .= '</div>';

    if (!empty($alerts)) {
        $html .= '<h2>Alertes automàtiques</h2>';
        foreach ($alerts as $alert) {
            $class = 'alert-' . htmlspecialchars($alert['level']);
            $html .= '<div class="alert ' . $class . '">';
            $html .= '<strong>' . htmlspecialchars($alert['title']) . '</strong><br>';
            $html .= htmlspecialchars($alert['message']);
            $html .= '</div>';
        }
    }

    $html .= '<h2>Desglossament per factors</h2>';
    $html .= '<table>';
    $html .= '<thead><tr><th>Factor</th><th>Valor</th><th>Pes</th><th>Aportació</th><th>Semàfor</th><th>Observacions</th><th>Justificació</th><th>Evidència</th></tr></thead><tbody>';

    foreach ($factorRows as $row) {
        $html .= '<tr>';
        $html .= '<td><strong>' . htmlspecialchars($row['label']) . '</strong></td>';
        $html .= '<td>' . (int) $row['selected_value'] . ' — ' . htmlspecialchars($row['selected_label']) . '</td>';
        $html .= '<td>' . (int) $row['weight'] . '%</td>';
        $html .= '<td>' . (int) $row['weighted_points'] . ' / ' . (int) $row['max_weighted_points'] . '</td>';
        $html .= '<td>' . htmlspecialchars($row['signal']['label']) . '</td>';
        $html .= '<td>' . nl2br(htmlspecialchars($row['note'])) . '</td>';
        $html .= '<td>' . nl2br(htmlspecialchars($row['justification'])) . '</td>';
        $html .= '<td>' . nl2br(htmlspecialchars($row['evidence'])) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    $html .= '<p class="small">Document generat automàticament per la calculadora de factors de sostenibilitat.</p>';
    $html .= '</body></html>';

    return $html;
}
