<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMI Calculation Result</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: Arial, sans-serif;
            /* background-color: #f4f4f4; */
        }
        .container {
            max-width: 800px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px; /* Add margin space at the top */
        }
        .container h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        canvas {
            max-width: 400px;
            max-height: 400px;
            margin: 20px auto;
            display: block;
        }
        .row:after {
            content: "";
            display: table;
            clear: both;
        }
        .col-md-6 {
            float: left;
            width: 50%;
        }
        .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container" >
        <h2>EMI Calculation Result</h2>
        <table class="table">
            <tr>
                <th>Serial</th>
                <th>Month</th>
                <th>Year</th>
                <th>EMI Amount</th>
                <th>Principal Amount</th>
                <th>Interest Amount</th>
                <th>Remaining Loan Amount</th>
            </tr>
            <?php
                // EMI calculation logic
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $loanAmount = $_POST["loan_amount"];
                    $annualInterestRate = $_POST["annual_interest_rate"];
                    $loanYears = $_POST["loan_years"];
                    $loanMonths = $_POST["loan_months"];
                    $currentYear = date('Y');
                    $currentMonth = date('n');

                    $totalMonths = $loanYears * 12 + $loanMonths;
                    $monthlyInterestRate = $annualInterestRate / (12 * 100);
                    $monthlyPayment = ($loanAmount * $monthlyInterestRate) / (1 - pow(1 + $monthlyInterestRate, -$totalMonths));

                    $remainingLoanAmount = $loanAmount;
                    $totalInterest = 0;

                    for ($i = 1; $i <= $totalMonths; $i++) {
                        $interest = $remainingLoanAmount * $monthlyInterestRate;
                        $principal = $monthlyPayment - $interest;
                        $totalInterest += $interest;
                        $remainingLoanAmount -= $principal;

                        // Calculate the month and year for current iteration
                        $currentIterationMonth = ($currentMonth + $i) % 12;
                        if ($currentIterationMonth == 0) {
                            $currentIterationMonth = 12;
                        }
                        $yearIncrement = floor(($currentMonth + $i - 1) / 12);

                        echo "<tr>";
                        echo "<td>" . $i . "</td>";
                        echo "<td>" . date("F", mktime(0, 0, 0, $currentIterationMonth, 1)) . "</td>";
                        echo "<td>" . ($currentYear + $yearIncrement) . "</td>";
                        echo "<td>" . number_format($monthlyPayment, 0) . "</td>"; // Display in whole numbers
                        echo "<td>" . number_format($principal, 0) . "</td>"; // Display in whole numbers
                        echo "<td>" . number_format($interest, 0) . "</td>"; // Display in whole numbers
                        echo "<td>" . number_format($remainingLoanAmount, 0) . "</td>"; // Display in whole numbers
                        echo "</tr>";
                    }
                }
            ?>
        </table>
        
        <h3>Additional Information:</h3>
        <div class="row">
            <div class="col-md-6">
            <table class="table">
            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $totalPrincipalAmount = $loanAmount;
                    $totalInterestAmount = $totalInterest;
                    $totalPayableAmount = $totalPrincipalAmount + $totalInterestAmount;

                    $percentagePrincipal = number_format(($totalPrincipalAmount / $totalPayableAmount) * 100, 2); // Percentage rounded to 2 decimal places
                    $percentageInterest = number_format(($totalInterestAmount / $totalPayableAmount) * 100, 2); // Percentage rounded to 2 decimal places

                    echo "<tr>";
                    echo "<td>Total Principal Amount:</td>";
                    echo "<td>Total Interest Amount:</td>";
                    echo "<td>Total Payable Amount:</td>";
                    // echo "<td>Percentage of Principal Loan Amount:</td>";
                    // echo "<td>Percentage of Total Interest Paid:</td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td>" . number_format($totalPrincipalAmount, 0) . "</td>";
                    echo "<td>" . number_format($totalInterestAmount, 0) . "</td>";
                    echo "<td>" . number_format($totalPayableAmount, 0) . "</td>";
                    // echo "<td>" . $percentagePrincipal . "%</td>";
                    // echo "<td>" . $percentageInterest . "%</td>";
                    echo "</tr>";
                }
            ?>
            </table>
            </div>

            <div class="col-md-6">
                <div style="text-align: center;">
                    <canvas id="myChart" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
        <button class="btn btn-primary" onclick="window.print()">Print</button>
        <button class="btn btn-secondary" id="pdfButton">Convert to PDF</button>
        <button class="btn btn-success" onclick="shareResult()">Share</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Principal Amount', 'Interest Amount'],
                datasets: [{
                    label: 'Percentage',
                    data: [<?php echo $percentagePrincipal; ?>, <?php echo $percentageInterest; ?>],
                    // label: '%',
                    backgroundColor: [
                        'rgba(255, 159, 64, 0.8)', // Orange color for Principal Amount
                        'rgba(75, 192, 192, 0.8)' // Green color for Interest Amount
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'Percentage of Principal Amount and Interest Amount'
                }
            }
        });

         // Convert to PDF button functionality
         document.getElementById('pdfButton').addEventListener('click', function() {
            var pdf = new jsPDF();
            pdf.text(20, 20, 'EMI Calculation Result');
            pdf.addHTML(document.body, function() {
                pdf.save('emi_calculation_result.pdf');
            });
        });

        // Share Functionality
        function shareResult() {
            if (navigator.share) {
                navigator.share({
                    title: 'EMI Calculation Result',
                    text: 'Check out the EMI Calculation Result.',
                    url: window.location.href
                }).then(() => {
                    console.log('EMI Calculation Result shared successfully.');
                }).catch((error) => {
                    console.error('Error sharing EMI Calculation Result:', error);
                });
            } else {
                alert('Web Share API is not supported in this browser.');
            }
        }

    </script>
</body>
</html>
