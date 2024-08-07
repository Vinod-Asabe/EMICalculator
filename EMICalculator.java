import java.text.DecimalFormat;
import java.util.Scanner;

public class EMICalculator {
    public static void main(String[] args) {
        Scanner input = new Scanner(System.in);

        System.out.print("Enter the loan amount: ");
        double principal = input.nextDouble();

        System.out.print("Enter the annual interest rate (in percentage): ");
        double annualInterestRate = input.nextDouble();

        System.out.print("Enter the loan term (in years): ");
        int years = input.nextInt();

        double monthlyInterestRate = annualInterestRate / (12 * 100);
        int months = years * 12;

        // Calculate EMI
        double monthlyPayment = (principal * monthlyInterestRate) / (1 - Math.pow(1 + monthlyInterestRate, -months));

        System.out.printf("The monthly EMI is: %.2f%n", monthlyPayment);

        DecimalFormat df = new DecimalFormat("#.##");

        // Calculate Amortization Schedule
        System.out.println("\nAmortization Schedule:");
        System.out.println("--------------------------------------------------");
        System.out.printf("%5s | %10s | %10s | %10s | %12s%n", "Month", "EMI", "Principal", "Interest", "Balance");
        System.out.println("--------------------------------------------------");

        double balance = principal;
        double totalInterest = 0;
        for (int i = 1; i <= months; i++) {
            double interest = balance * monthlyInterestRate;
            double principalPaid = monthlyPayment - interest;
            totalInterest += interest;
            balance =balance - principalPaid;
            if (balance < 0) {
                balance = 0;
            }
            System.out.printf("%5d | %10s | %10s | %10s | %12s%n", i, df.format(monthlyPayment), df.format(principalPaid), df.format(interest), df.format(balance));
        }
        System.out.println("-----------------------------------------------------------");

        

        double totalPrincipalCost = principal;
        double totalInterestCost = totalInterest;
        double totalPayableAmount = totalPrincipalCost + totalInterestCost;

        System.out.println("\nCost of Total Principal Amount: " + df.format(totalPrincipalCost));
        System.out.println("Cost of Total Interest: " + df.format(totalInterestCost));
        System.out.println("Total Payable Amount: " + df.format(totalPayableAmount));

        double percentagePrincipal = (principal / (principal + totalInterest)) * 100;
        double percentageInterest = (totalInterest / (principal + totalInterest)) * 100;

        System.out.println("Percentage of Principal Loan Amount: " + df.format(percentagePrincipal) + "%");
        System.out.println("Percentage of Total Interest Paid: " + df.format(percentageInterest) + "%");


        input.close();
    }
}
