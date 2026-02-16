<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yantra Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="bg-pink">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <span>Welcome: <strong id="unique_id_f">2101400001</strong></span>
            <span>Balance Point: <strong>6.48</strong></span>
        </div>
        <div class="mt-3">
            <div class="btn-group w-100" role="group">
                <button type="button" class="btn btn-outline-primary">Bulk Coupons [A]</button>
                <button type="button" class="btn btn-primary">Bulk Coupons [B]</button>
                <button type="button" class="btn btn-outline-secondary">Pointwise Coupons</button>
                <button type="button" class="btn btn-outline-primary">Loose Coupons</button>
                <button type="button" class="btn btn-outline-secondary">Loose Pointwise Coupons</button>
            </div>
        </div>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm text-center">
                <thead class="bg-light">
                    <tr>
                        <th>Product Group</th>
                        <th>Gift Coin</th>
                        <th>00-09</th>
                        <th>10-19</th>
                        <th>20-29</th>
                        <th>30-39</th>
                        <th>40-49</th>
                        <th>50-59</th>
                        <th>60-69</th>
                        <th>70-79</th>
                        <th>80-89</th>
                        <th>90-99</th>
                        <th>All</th>
                        <th>Qty</th>
                        <th>Pts</th>
                        <th>Draw</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Yantra Group NV</td>
                        <td>-</td>
                        <td><input type="text" class="form-control"></td>
                        <!-- Repeat for other cells -->
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Yantra Group RR</td>
                        <td>-</td>
                        <td><input type="text" class="form-control"></td>
                        <!-- Repeat for other cells -->
                        <td>-</td>
                    </tr>
                    <!-- Repeat for other rows -->
                    <tr>
                        <td>All</td>
                        <td>-</td>
                        <td><input type="text" class="form-control"></td>
                        <!-- Repeat for other cells -->
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="15" class="text-end">TOTAL</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-success">Upcoming</button>
            <button class="btn btn-primary">Buy</button>
            <button class="btn btn-danger">Clear</button>
            <button class="btn btn-warning">Cancel</button>
            <button class="btn btn-secondary">Yantra</button>
            <button class="btn btn-info">C.Details</button>
            <button class="btn btn-dark">P.Sum</button>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <span>Date: 26/07/2024</span>
            <span class="ms-3">Gift Event Code: 08:30</span>
            <span class="ms-3">Countdown: 00:21:56</span>
        </div>
    </div>
</body>
<script>
    document.getElementById("unique_id_f").innerHTML =JSON.parse(localStorage.getItem("loginInfo")).unique_id
</script>
</html>
