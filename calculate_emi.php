<!DOCTYPE html>
<html>
<head>
    <title>EMI Calculation Result</title>
</head>
<body>
    <h2>EMI Calculation Result</h2>
    <table border="1">
        <tr>
            <th>Month</th>
            <th>EMI Amount</th>
            <th>Principal Amount</th>
            <th>Interest Amount</th>
            <th>Remaining Loan Amount</th>
        </tr>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $loanAmount = $_POST["loan_amount"];
            $annualInterestRate = $_POST["annual_interest_rate"];
            $loanTerm = $_POST["loan_term"];

            $monthlyInterestRate = $annualInterestRate / (12 * 100);
            $months = $loanTerm * 12;

            $monthlyPayment = ($loanAmount * $monthlyInterestRate) / (1 - pow(1 + $monthlyInterestRate, -$months));

            $remainingLoanAmount = $loanAmount;

            for ($i = 1; $i <= $months; $i++) {
                $interest = $remainingLoanAmount * $monthlyInterestRate;
                $principal = $monthlyPayment - $interest;
                $remainingLoanAmount = $remainingLoanAmount - $principal;

                echo "<tr>";
                echo "<td>$i</td>";
                echo "<td>".number_format($monthlyPayment, 2)."</td>";
                echo "<td>".number_format($principal, 2)."</td>";
                echo "<td>".number_format($interest, 2)."</td>";
                echo "<td>".number_format($remainingLoanAmount, 2)."</td>";
                echo "</tr>";
            }
        }
        ?>
    </table>
    <br>
    <form action="/generate_chart" method="post">
    <input type="hidden" name="loan_amount" value="<?php echo $loanAmount; ?>">
    <input type="hidden" name="monthly_payment" value="<?php echo $monthlyPayment; ?>">
    <input type="submit" value="View Chart">
</form>

</body>
</html>
