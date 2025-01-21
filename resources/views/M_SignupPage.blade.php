<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/M_signup.css') }}">
    <title>Signup Page</title>
</head>
<body>
    <div class="main-container">
        <div class="Signup-header">
            <p>Sign up</p>
        </div>
        <form id="Signup-form" action="{{ route('signup.submit') }}" method="POST">
            @csrf 
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <div class="password-error" style="color: red; display: none;"></div><br>

            <label for="password_confirm">Confirm Password:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
            <div class="password-error" style="color: red; display: none;"></div><br>

            <label for="desasiswa">Desasiswa:</label>
            <select id="desasiswa" name="desasiswa" required>
                <option value="">--Select Desasiswa--</option>
                @foreach( $desasiswas as $desasiswa ) 
                <option value="{{$desasiswa->desasiswa_id}}">{{$desasiswa->desasiswa_name}}</option>
                @endforeach
            </select><br>

            <label for="role">Choose Role:</label>
            <select id="role" name="role" onchange="toggleFields()" required>
                <option value="">--Select Role--</option>
                <option value="student">Student</option>
                <option value="team_manager">Team Manager</option>
            </select><br>

            <!-- Conditional fields for student role -->
            <div id="student-fields" style="display:none;">
                <label for="matrics_no">Matrics Number:</label>
                <input type="text" id="matrics_no" name="matrics_no" maxlength="6" pattern="\d{6}" placeholder="e.g 165797"><br>
            </div>

            <!-- Conditional fields for team manager role -->
            <div id="manager-fields" style="display:none;">
                <label for="registration_code">Registration Code:</label>
                <input type="text" id="registration_code" name="registration_code"><br>
            </div>

            <input type="submit" value="Sign Up">
        </form>
        @if ($errors->any())
        <div class="error-messages">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif
        <div class="login-link">
            <p>Already have an account? <a href="{{route('login')}}">Log in here</a></p>
        </div>
    </div>
</body>
</html>

<script>
   function toggleFields() {
    const role = document.getElementById('role').value;
    const studentFields = document.getElementById('student-fields');
    const managerFields = document.getElementById('manager-fields');
    const matricsNo = document.getElementById('matrics_no');
    const registrationCode = document.getElementById('registration_code');
    
    if (role === 'student') {
        studentFields.style.display = 'block';
        managerFields.style.display = 'none';
        matricsNo.required = true;
        registrationCode.required = false; // Remove required attribute
        registrationCode.value = ''; // Clear input value
    } else if (role === 'team_manager') {
        studentFields.style.display = 'none';
        managerFields.style.display = 'block';
        matricsNo.required = false; // Remove required attribute
        matricsNo.value = ''; // Clear input value
        registrationCode.required = true;
    } else {
        // Reset both fields when no role is selected
        studentFields.style.display = 'none';
        managerFields.style.display = 'none';
        matricsNo.required = false;
        registrationCode.required = false;
        matricsNo.value = '';
        registrationCode.value = '';
    }
}

    document.getElementById('name').addEventListener('input', function(event) {
        const nameField = event.target;
        const nameValue = nameField.value;

        // Regular expression to allow only letters, spaces, and '/'
        const regex = /^[a-zA-Z\s/]*$/;

        // If the value does not match the allowed pattern
        if (!regex.test(nameValue)) {
            // Remove the last character (special character or number) from the input
            nameField.value = nameValue.replace(/[^a-zA-Z\s/]/g, "");
        }
    });

    document.getElementById('Signup-form').addEventListener('submit', function(event) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm').value;
        const errorMessages = document.querySelectorAll('.password-error');

        // Reset any previous error messages
        errorMessages.forEach(function(errorMessage) {
            errorMessage.style.display = 'none';
        });

        // Check if passwords match
        if (password !== passwordConfirm) {
            event.preventDefault(); // Prevent form submission
            errorMessages.forEach(function(errorMessage) {
                errorMessage.textContent = 'Passwords do not match.';
                errorMessage.style.display = 'block';
            });
        }
    });
</script>
