import io
import base64
import matplotlib.pyplot as plt
from flask import Flask, render_template

app = Flask(__name__)

@app.route('/generate_chart', methods=['GET', 'POST'])
def generate_chart():
    # Assuming loan_amount and monthly_payment are obtained from the form submission
    loan_amount = 10000  # Example loan amount
    monthly_payment = 500  # Example monthly payment
    months = 12  # Example number of months

    # Generate pie chart
    amounts = [monthly_payment * months, loan_amount - (monthly_payment * months)]
    labels = ['Paid Amount', 'Remaining Amount']
    fig, ax = plt.subplots()
    ax.pie(amounts, labels=labels, autopct='%1.1f%%')
    ax.set_title('Loan Amount Status')

    # Save the plot to a BytesIO object and encode as base64
    img = io.BytesIO()
    plt.savefig(img, format='png')
    plt.close(fig)
    img.seek(0)
    img_base64 = base64.b64encode(img.getvalue()).decode()

    # Pass the base64 string to the Flask template for display
    return render_template('generate_chart.html', img_base64=img_base64)

if __name__ == '__main__':
    app.run()
