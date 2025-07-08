<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Gateway with Google Pay</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        #payment-result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            display: none;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        #google-pay-button-container {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Payment Gateway</h1>
    <p>Total amount: $1.00 USD</p>
    
    <div id="google-pay-button-container"></div>
    
    <div id="payment-result"></div>

    <script async src="https://pay.google.com/gp/p/js/pay.js" onload="onGooglePayLoaded()"></script>
    
    <script>
        // 全局變量
        let googlePayClient;
        let paymentDataRequest;
        
        // Google Pay 加載完成後調用
        function onGooglePayLoaded() {
            // 初始化 PaymentsClient
            googlePayClient = new google.payments.api.PaymentsClient({
                environment: 'TEST'
            });
            
            // 檢查是否準備好支付
            checkReadyToPay();
        }
        
        // 檢查是否準備好使用 Google Pay
        function checkReadyToPay() {
            const clientConfiguration = {
                apiVersion: 2,
                apiVersionMinor: 0,
                allowedPaymentMethods: [cardPaymentMethod]
            };
            
            googlePayClient.isReadyToPay(clientConfiguration)
                .then(function(response) {
                    if (response.result) {
                        // 顯示 Google Pay 按鈕
                        const button = googlePayClient.createButton({
                            buttonColor: 'black',
                            buttonType: 'buy',
                            onClick: onGooglePaymentButtonClicked
                        });
                        document.getElementById('google-pay-button-container').appendChild(button);
                    } else {
                        showPaymentResult('Google Pay is not available in your browser or you have no payment methods setup.', false);
                    }
                })
                .catch(function(err) {
                    console.error('Error determining readiness to use Google Pay:', err);
                    showPaymentResult('Error checking Google Pay availability: ' + err.message, false);
                });
        }
        
        // Google Pay 按鈕點擊事件
        function onGooglePaymentButtonClicked() {
            loadPaymentData();
        }
        
        // 加載支付數據
        function loadPaymentData() {
            googlePayClient.loadPaymentData(getPaymentDataRequest())
                .then(function(paymentData) {
                    console.log('Payment data received:', paymentData);
                    showPaymentResult('Payment successful! Processing your order...', true);
                    processPayment(paymentData);
                })
                .catch(function(err) {
                    console.error('Error loading payment data:', err);
                    showPaymentResult('Payment failed: ' + err.message, false);
                });
        }
        
        // 處理支付
        function processPayment(paymentData) {
            // 這裡應該將支付數據發送到你的服務器進行處理
            // 這只是一個模擬的處理過程
            setTimeout(function() {
                showPaymentResult('Payment processed successfully! Transaction ID: ' + 
                    paymentData.paymentMethodData.tokenizationData.token, true);
            }, 2000);
        }
        
        // 獲取支付數據請求配置
        function getPaymentDataRequest() {
            return {
                apiVersion: 2,
                apiVersionMinor: 0,
                allowedPaymentMethods: [cardPaymentMethod],
                merchantInfo: {
                    merchantId: '01234567890123456789',
                    merchantName: 'Example Merchant'
                },
                transactionInfo: {
                    totalPriceStatus: 'FINAL',
                    totalPriceLabel: 'Total',
                    totalPrice: '1.00',
                    currencyCode: 'USD',
                    countryCode: 'US'
                }
            };
        }
        
        // 卡支付方法配置
        const cardPaymentMethod = {
            type: 'CARD',
            parameters: {
                allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                allowedCardNetworks: ['AMEX', 'DISCOVER', 'JCB', 'MASTERCARD', 'VISA'],
                billingAddressRequired: true,
                billingAddressParameters: {
                    format: 'FULL',
                    phoneNumberRequired: true
                }
            },
            tokenizationSpecification: {
                type: 'PAYMENT_GATEWAY',
                parameters: {
                    gateway: 'example',
                    gatewayMerchantId: 'exampleGatewayMerchantId'
                }
            }
        };
        
        // 顯示支付結果
        function showPaymentResult(message, isSuccess) {
            const resultDiv = document.getElementById('payment-result');
            resultDiv.textContent = message;
            resultDiv.className = isSuccess ? 'success' : 'error';
            resultDiv.style.display = 'block';
        }
    </script>
</body>
</html>