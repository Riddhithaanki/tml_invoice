<!DOCTYPE html>
<html>
<head>
    <title>User Data PDF</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        .user-data {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .user-photo img {
            max-width: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .user-section {
            margin-bottom: 20px;
        }
        .second-sec {
            margin-top: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="user-data">
            <div class="user-photo text-center">
                <img src="{{ $data['profile_photo_url'] ?? asset('default-photo.jpg') }}" alt="Profile Photo">
            </div>
            <div class="user-section">
                <span class="section-title">Id:</span> {{ $data['id'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">Full Name:</span> {{ $data['name'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">Address:</span> {{ $data['employee_details']['address'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">Age:</span> {{ $data['employee_details']['age'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">D.O.B.:</span> {{ $data['employee_details']['dob'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">Mobile Number:</span> {{ $data['employee_details']['mobile_number'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">Email:</span> {{ $data['email'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">Passport Number:</span> {{ $data['employee_details']['passport_number'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">Visa Status:</span>
            </div>
            <div class="user-section">
                <span class="section-title">Hours Allowed to Work:</span> {{ $data['employee_details']['hours_allowed_to_work'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">Bank BSB:</span> {{ $data['employee_details']['bank_bsb'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">Bank Account Number:</span> {{ $data['employee_details']['bank_account_number'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">ABN:</span> {{ $data['employee_details']['abnumber'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">Registered for GST:</span> {{ $data['employee_details']['is_registered_for_gst'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">State:</span> {{ $data['employee_details']['license_state'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">License Number:</span> {{ $data['employee_details']['license_number'] ?? "" }}
            </div>
            <div class="user-section">
                <span class="section-title">Employee Category:</span> {{ $data['employee_details']['employee_category'] ?? "" }}
            </div>

        </div>

        <div class="second-sec">
            <div class="user-section">
                <span class="section-title">Certification:</span> I Certify that the above details are true and correct and related to me.
            </div>
            <div class="user-section">
                <span class="section-title">Authorisation for GST:</span> I authorise Think Big Accountant to register me for GST.
            </div>
            <div class="user-section">
                <span class="section-title">Terms and Conditions:</span> I agree to the terms and conditions of subcontracting to Jays freight.
            </div>
        </div>
    </div>
</body>
</html>
