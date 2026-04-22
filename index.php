<?php
declare(strict_types=1);

date_default_timezone_set('Europe/Madrid');

require_once __DIR__ . '/includes/mb_polyfill.php';
require_once __DIR__ . '/vendor/dompdf/autoload.inc.php';
require_once __DIR__ . '/includes/functions.php';

use Dompdf\Dompdf;

$factors = require __DIR__ . '/config/factors.php';
$presets = require __DIR__ . '/config/presets.php';

$formatName = '';
$formatVersion = '';
$studentName = '';
$rawValues = [];
$rawNotes = [];
$rawJustifications = [];
$rawEvidence = [];
$result = null;
$errors = [];
$factorRows = [];
$alerts = [];
$totalWeight = array_sum(array_column($factors, 'weight'));

foreach ($factors as $key => $data) {
    $rawValues[$key] = 3;
    $rawNotes[$key] = '';
    $rawJustifications[$key] = '';
    $rawEvidence[$key] = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'view';

    $formatName = sanitizeText($_POST['format'] ?? '');
    $formatVersion = sanitizeText($_POST['version'] ?? '');
    $studentName = sanitizeText($_POST['student'] ?? '');

    foreach ($factors as $key => $data) {
        $rawValues[$key] = isset($_POST[$key]) ? (int) $_POST[$key] : 3;
        $rawNotes[$key] = sanitizeText($_POST[$key . '_note'] ?? '');
        $rawJustifications[$key] = sanitizeText($_POST[$key . '_justification'] ?? '');
        $rawEvidence[$key] = sanitizeText($_POST[$key . '_evidence'] ?? '');
    }

    if ($formatName === '') {
        $errors['format'] = 'Has d\'indicar el nom del format.';
    }

    if ($studentName === '') {
        $errors['student'] = 'Has d\'indicar l\'autor/a o estudiant.';
    }

    $errors = array_merge($errors, validateSubmission($factors, $_POST));

    if (empty($errors)) {
        $result = calculateResult($factors, $rawValues);
        $factorRows = getFactorRows($factors, $rawValues, $rawNotes, $rawJustifications, $rawEvidence, $totalWeight);
        $alerts = buildAlerts($rawValues, (float) $result['score']);

        $meta = [
            "format" => $formatName,
            "version" => $formatVersion,
            "student" => $studentName,
            "evaluation_date_human" => getCurrentDateTimeString(),
            "evaluation_date_iso" => getCurrentIsoDateTimeString()
        ];

        if ($action === 'download_json') {
            $payload = buildJsonPayload($meta, $result, $factorRows, $alerts);

            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="avaluacio_format.json"');

            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        if ($action === 'download_pdf') {
            $html = renderPdfHtml($meta, $result, $factorRows, $alerts);

            $dompdf = new Dompdf([
                'isRemoteEnabled' => true
            ]);

            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $dompdf->stream('avaluacio_format.pdf', ['Attachment' => true]);
            exit;
        }
    }
}

if ($result && empty($factorRows)) {
    $factorRows = getFactorRows($factors, $rawValues, $rawNotes, $rawJustifications, $rawEvidence, $totalWeight);
    $alerts = buildAlerts($rawValues, (float) $result['score']);
}

$chartCategories = [];
$chartValues = [];
if ($result) {
    foreach ($factors as $key => $data) {
        $chartCategories[] = $data['label'];
        $chartValues[] = $rawValues[$key] ?? 0;
    }
}
?>
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calculadora de factors de sostenibilitat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">

    <script>
        window.APP_PRESETS = <?php echo json_encode($presets, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="mb-2">Calculadora de factors de sostenibilitat</h1>
            <p class="mb-0 text-secondary">Eina docent per valorar formats digitals segons factors de sostenibilitat i preservar millor el raonament de l'avaluació.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" id="load-epub-preset" class="btn btn-outline-primary">Carrega la fitxa orientativa d'EPUB</button>
        </div>
    </div>

    <div id="preset-preview" class="card preset-card shadow-sm mb-4 d-none">
        <div class="card-body">
            <h2 class="h5 preset-title mb-2"></h2>
            <p class="preset-summary mb-0"></p>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <h2 class="h5">Cal revisar alguns camps</h2>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm mb-4" novalidate>
        <div class="row g-3 mb-2">
            <div class="col-md-4">
                <label class="form-label" for="format">Format</label>
                <input type="text" id="format" name="format" class="form-control <?php echo isset($errors['format']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($formatName); ?>" required>
                <?php if (isset($errors['format'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['format']); ?></div><?php endif; ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="version">Versió</label>
                <input type="text" id="version" name="version" class="form-control" value="<?php echo htmlspecialchars($formatVersion); ?>" placeholder="Ex. 3.3">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="student">Autor/a o estudiant</label>
                <input type="text" id="student" name="student" class="form-control <?php echo isset($errors['student']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($studentName); ?>" required>
                <?php if (isset($errors['student'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['student']); ?></div><?php endif; ?>
            </div>
        </div>

        <div class="alert alert-info mt-3 mb-4">
            <strong>Com interpretar l'escala:</strong> 0 = més risc i 4 = menys risc. Si marques 0 o 1 en algun factor, la justificació és obligatòria.
        </div>

        <div class="row g-4">
            <?php foreach ($factors as $key => $data): ?>
                <?php
                    $currentVal = $rawValues[$key] ?? 3;
                    $currentNote = $rawNotes[$key] ?? '';
                    $currentJustification = $rawJustifications[$key] ?? '';
                    $currentEvidence = $rawEvidence[$key] ?? '';
                ?>
                <div class="col-12">
                    <div class="factor-card shadow-sm">
                        <div class="card-body">
                            <div class="factor-header mb-3">
                                <div>
                                    <h2 class="h5 mb-1"><?php echo htmlspecialchars($data['label']); ?></h2>
                                    <p class="small-muted mb-0">Pes del factor: <?php echo (int) $data['weight']; ?>%</p>
                                </div>
                                <span class="badge text-bg-light border weight-pill"><?php echo (int) $data['weight']; ?>%</span>
                            </div>

                            <div class="card card-help mb-3">
                                <div class="card-body py-3">
                                    <h3 class="h6">Ajuda contextual</h3>
                                    <p class="mb-2"><?php echo htmlspecialchars($data['help']); ?></p>
                                    <details>
                                        <summary class="fw-semibold">Exemples orientatius de puntuació</summary>
                                        <ul class="mt-2 mb-0">
                                            <?php foreach ($data['examples'] as $score => $example): ?>
                                                <li><strong><?php echo (int) $score; ?>:</strong> <?php echo htmlspecialchars($example); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </details>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label" for="<?php echo $key; ?>">Valoració</label>
                                    <select id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="form-select factor-select" data-factor="<?php echo $key; ?>" required>
                                        <?php foreach ($data['options'] as $value => $label): ?>
                                            <option value="<?php echo $value; ?>" <?php echo ((int)$value === (int)$currentVal) ? 'selected' : ''; ?>>
                                                <?php echo $value . ' — ' . htmlspecialchars($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-lg-8">
                                    <label class="form-label" for="<?php echo $key; ?>_evidence">Evidència o font</label>
                                    <input type="text" id="<?php echo $key; ?>_evidence" name="<?php echo $key; ?>_evidence" class="form-control" value="<?php echo htmlspecialchars($currentEvidence); ?>" placeholder="URL, norma, article, documentació, prova tècnica...">
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label" for="<?php echo $key; ?>_note">Observacions</label>
                                    <textarea id="<?php echo $key; ?>_note" name="<?php echo $key; ?>_note" class="form-control" placeholder="Afegeix observacions o matisos de l'avaluació"><?php echo htmlspecialchars($currentNote); ?></textarea>
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label" for="<?php echo $key; ?>_justification">Justificació</label>
                                    <textarea id="<?php echo $key; ?>_justification" name="<?php echo $key; ?>_justification" class="form-control <?php echo isset($errors[$key . '_justification']) ? 'is-invalid' : ''; ?>" placeholder="Obligatori si selecciones 0 o 1"><?php echo htmlspecialchars($currentJustification); ?></textarea>
                                    <div id="<?php echo $key; ?>-justification-helper" class="form-text small-justification">Només és obligatori si marques 0 o 1.</div>
                                    <?php if (isset($errors[$key . '_justification'])): ?>
                                        <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors[$key . '_justification']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="sticky-actions d-flex gap-2 flex-wrap">
            <button type="submit" name="action" value="view" class="btn btn-primary">Calcular risc</button>
            <?php if ($result && empty($errors)): ?>
                <button type="submit" name="action" value="download_json" class="btn btn-outline-secondary">Descarregar JSON</button>
                <button type="submit" name="action" value="download_pdf" class="btn btn-outline-secondary">Descarregar PDF</button>
            <?php endif; ?>
        </div>
    </form>

    <?php if ($result && empty($errors)): ?>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="result-kpi">
                    <h2 class="h6 text-secondary">Puntuació ponderada</h2>
                    <p class="display-6 mb-0"><?php echo (int) $result['score']; ?>%</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="result-kpi">
                    <h2 class="h6 text-secondary">Valoració global</h2>
                    <p class="display-6 mb-0"><?php echo htmlspecialchars($result['risk']); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="result-kpi">
                    <h2 class="h6 text-secondary">Puntuació bruta</h2>
                    <p class="display-6 mb-0"><?php echo (int) $result['total_score']; ?>/<?php echo (int) $result['max_score']; ?></p>
                </div>
            </div>
        </div>

        <?php if (!empty($alerts)): ?>
            <div class="mb-4">
                <h2 class="h4 mb-3">Alertes automàtiques</h2>
                <?php foreach ($alerts as $alert): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($alert['level']); ?>">
                        <strong><?php echo htmlspecialchars($alert['title']); ?></strong><br>
                        <?php echo htmlspecialchars($alert['message']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h4 mb-3">Desglossament del pes real i semàfor per factor</h2>
                <p class="small">Nota: el pes relatiu de cada factor respon a un criteri expert i, per tant, incorpora un component subjectiu. Aquesta ponderació té una finalitat docent i orientativa, i no s'ha d'interpretar com una distribució universal o definitiva.</p>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th>Factor</th>
                            <th>Valor</th>
                            <th>Pes</th>
                            <th>Aportació</th>
                            <th>Semàfor</th>
                            <th>Evidència</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($factorRows as $row): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['label']); ?></strong><br>
                                    <span class="small text-secondary"><?php echo htmlspecialchars($row['selected_label']); ?></span>
                                </td>
                                <td><?php echo (int) $row['selected_value']; ?>/4</td>
                                <td>
                                    <?php echo (int) $row['weight']; ?>%<br>
                                    <span class="small text-secondary">Pes relatiu sobre el total</span>
                                </td>
                                <td>
                                    <?php echo (int) $row['weighted_points']; ?> / <?php echo (int) $row['max_weighted_points']; ?><br>
                                    <span class="small text-secondary">Punts ponderats assolits</span>
                                </td>
                                <td>
                                    <span class="signal-badge <?php echo htmlspecialchars($row['signal']['class']); ?>">
                                        <span class="signal-dot" aria-hidden="true"></span>
                                        <?php echo htmlspecialchars($row['signal']['label']); ?>
                                    </span>
                                </td>
                                <td><?php echo $row['evidence'] !== '' ? nl2br(htmlspecialchars($row['evidence'])) : '<span class="text-secondary">—</span>'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm chart-card mb-4">
            <div class="card-body">
                <h2 class="h4 mb-3">Gràfic radar dels factors</h2>
                <div id="radar-chart" style="height: 420px;"></div>
            </div>
        </div>

        <div class="accordion mb-4" id="detailAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingDetails">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetails" aria-expanded="false" aria-controls="collapseDetails">
                        Veure observacions, justificacions i evidències
                    </button>
                </h2>
                <div id="collapseDetails" class="accordion-collapse collapse" aria-labelledby="headingDetails" data-bs-parent="#detailAccordion">
                    <div class="accordion-body">
                        <?php foreach ($factorRows as $row): ?>
                            <div class="mb-4">
                                <h3 class="h5"><?php echo htmlspecialchars($row['label']); ?></h3>
                                <p><strong>Observacions:</strong><br><?php echo $row['note'] !== '' ? nl2br(htmlspecialchars($row['note'])) : '—'; ?></p>
                                <p><strong>Justificació:</strong><br><?php echo $row['justification'] !== '' ? nl2br(htmlspecialchars($row['justification'])) : '—'; ?></p>
                                <p><strong>Evidència:</strong><br><?php echo $row['evidence'] !== '' ? nl2br(htmlspecialchars($row['evidence'])) : '—'; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const chartContainer = document.getElementById('radar-chart');
                if (!chartContainer || typeof echarts === 'undefined') return;

                const radarChart = echarts.init(chartContainer);
                const categories = <?php echo json_encode($chartCategories, JSON_UNESCAPED_UNICODE); ?>;
                const values = <?php echo json_encode($chartValues); ?>;

                radarChart.setOption({
                    title: {
                        text: 'Valors per factor (0 = més risc, 4 = menys risc)',
                        left: 'center'
                    },
                    tooltip: {},
                    legend: {
                        data: ['Valoració'],
                        bottom: 0
                    },
                    radar: {
                        center: ['50%', '50%'],
                        radius: '65%',
                        indicator: categories.map(name => ({ name, max: 4 })),
                        splitNumber: 4
                    },
                    series: [{
                        name: 'Valoració',
                        type: 'radar',
                        data: [{
                            value: values,
                            name: 'Valoració'
                        }],
                        areaStyle: {
                            opacity: 0.2
                        }
                    }]
                });

                window.addEventListener('resize', () => radarChart.resize());
            });
        </script>
    <?php endif; ?>

    <footer class="mt-4 pt-3 border-top text-secondary">
        <p class="mb-0">&copy; Rubén Alcaraz Martínez. Eina docent per a l'assignatura Informació i Formats Digitals.</p>
        <p>Basat en les descripcions de Library of Congress (2025). <a target="_blank" href="https://www.loc.gov/preservation/digital/formats/sustain/sustain.shtml">Sustainability of Digital Formats: Planning for Library of Congress Collections</a>.</p>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
