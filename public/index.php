<?php
declare(strict_types=1);

use Cronixweb\Streamline\Streamline;

// Composer autoload
require __DIR__ . '/../vendor/autoload.php';

// Read credentials from environment. Avoid hardcoding in browser-accessible code.
$apiKey    = 'bdcb420297c6d8d16144deab07d23d76';
$apiSecret = '745b0e91f5beb354a3d7f19f48df4a92f0a5b018';

if (!$apiKey || !$apiSecret) {
    http_response_code(500);
    echo '<h2>Missing credentials</h2><p>Set STREAMLINE_TOKEN_KEY and STREAMLINE_TOKEN_SECRET in your environment.</p>';
    exit;
}

$api = Streamline::api($apiKey, $apiSecret);

// Simple routing based on query params
$action = $_GET['action'] ?? '';
$unitId = isset($_GET['unitId']) ? (int) $_GET['unitId'] : 0;
$start  = $_GET['start'] ?? '';
$end    = $_GET['end'] ?? '';

function respond_json($data) {
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}
try {
    switch ($action) {
        case 'property':
//            if (!$unitId) throw new InvalidArgumentException('unitId is required');
//            $data = $api->properties()->find($unitId);
            $data = $api->properties()->all();
            var_dump($data);die;
            respond_json(['ok' => true, 'data' => $data]);

        case 'amenities':
            if (!$unitId) throw new InvalidArgumentException('unitId is required');
            $data = $api->properties()->amenities($unitId)->all();
            respond_json(['ok' => true, 'data' => $data]);

        case 'reviews':
            if (!$unitId) throw new InvalidArgumentException('unitId is required');
            $data = $api->properties()->reviews($unitId)->all();
            respond_json(['ok' => true, 'data' => $data]);

        case 'images':
            if (!$unitId) throw new InvalidArgumentException('unitId is required');
            $data = $api->properties()->galleryImages($unitId)->all();
            respond_json(['ok' => true, 'data' => $data]);

        case 'booked':
            if (!$unitId) throw new InvalidArgumentException('unitId is required');
            if (!$start || !$end) throw new InvalidArgumentException('start and end dates are required (mm/dd/yyyy)');
            $data = $api->bookedDates()->all([
                'unit_id'  => $unitId,
                'startDate'=> $start,
                'endDate'  => $end,
            ]);
            respond_json(['ok' => true, 'data' => $data]);

        case 'rates':
            if (!$unitId) throw new InvalidArgumentException('unitId is required');
            if (!$start || !$end) throw new InvalidArgumentException('start and end dates are required (mm/dd/yyyy)');
            $data = $api->propertyRates()->all([
                'unit_id'  => $unitId,
                'startDate'=> $start,
                'endDate'  => $end,
            ]);
            respond_json(['ok' => true, 'data' => $data]);

        default:
            // Show a simple HTML UI
            ?>
            <!doctype html>
            <html lang="en">
            <head>
                <meta charset="utf-8" />
                <title>Streamline SDK Browser Test</title>
                <style> body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; margin:20px;} label{display:block;margin:.5em 0 .2em;} input,select{padding:.4em;} .row{margin-bottom:.8em;} code{background:#f4f4f4;padding:.2em .4em;border-radius:4px;} </style>
            </head>
            <body>
            <h1>Streamline SDK Browser Test</h1>
            <form method="get" onsubmit="return true;">
                <div class="row">
                    <label>Action</label>
                    <select name="action">
                        <option value="property">Property info</option>
                        <option value="amenities">Amenities</option>
                        <option value="reviews">Reviews</option>
                        <option value="images">Gallery Images</option>
                        <option value="booked">Booked/Blocked Dates</option>
                        <option value="rates">Property Rates</option>
                    </select>
                </div>
                <div class="row">
                    <label>Unit ID</label>
                    <input type="number" name="unitId" value="<?php echo htmlspecialchars((string)$unitId ?: '28254'); ?>" required>
                </div>
                <div class="row">
                    <label>Start Date (mm/dd/yyyy)</label>
                    <input type="text" name="start" value="<?php echo htmlspecialchars($start ?: '12/05/2019'); ?>">
                </div>
                <div class="row">
                    <label>End Date (mm/dd/yyyy)</label>
                    <input type="text" name="end" value="<?php echo htmlspecialchars($end ?: '12/05/2020'); ?>">
                </div>
                <button type="submit">Run</button>
            </form>
            <p>Results will be returned as JSON. You can also call endpoints directly, e.g. <code>?action=property&unitId=28254</code>.</p>
            </body>
            </html>
            <?php
            exit;
    }
} catch (Throwable $e) {
    respond_json([
        'ok' => false,
        'error' => [
            'message' => $e->getMessage(),
            'type' => get_class($e),
        ],
    ]);
}
