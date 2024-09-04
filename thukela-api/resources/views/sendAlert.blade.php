<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <style>
        table {
            border-collapse: collapse;
            border: 1px solid #d3d3d3;
            width: 100%;
        }

        th, td {
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {background-color: #f2f2f2;}
    </style>
</head>
<body>
<div style="overflow-x: auto; border:1px solid #d3d3d3; padding:10px;">
    <div style="text-align:center;">
        <h2 style="text-transform:uppercase"><b>Thukela Metering</b></h2>
        <h4 style="text-transform:uppercase"><b>Consumption Alert</b></h4>
    </div>
    <div>
        <table>
            <tr>
                <td>
                    <p>Hello</p>
                    <p><b>User Name:</b> {{$data['username']}} </p>
                    <!--<p>This user usage more than 30% consumption this week</p>-->
                    <p><b>Dear client please take note that your current usage has increased above your normal average usage</b></p>
                    <p><b>Previous Consumption:</b> {{$data['previousConsumption']}} </p>
                    <p><b>Current Consumption:</b> {{$data['currentConsumption']}} </p>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>


