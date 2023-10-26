<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reservation Report</title>
</head>
<body>
    <center>
        <table border="1" cellspacing="0" cellpadding="5" width="800" class="datatable" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
            <thead>
                <tr>
                    <th>Reserved User Name</th>
                    <th>Tour Type</th>
                    <th>Trip Date</th>
                    <th>Status</th>
                    <th>Pax</th>
                    <th>Ticket Pass</th>
                    <th>Discount</th>
                    <th>Sub Amount</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tour_reservations as $tour_reservation)
                    <tr>
                        <td>{{ $tour_reservation->user->firstname ?? null }} {{ $tour_reservation->user->lastname ?? null }}</td>
                        <td>{{ $tour_reservation->type }}</td>
                        <td>{{ $tour_reservation->start_date ? date_format(new DateTime($tour_reservation->start_date), 'M d, Y') : null }}</td>
                        <td>{{ $tour_reservation->status }}</td>
                        <td>{{ $tour_reservation->number_of_pass }}</td>
                        <td>{{ $tour_reservation->ticket_pass }}</td>
                        <td>{{ number_format($tour_reservation->discount, 2) }}</td>
                        <td>{{ number_format($tour_reservation->sub_amount, 2) }}</td>
                        <td>{{ number_format($tour_reservation->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" align="center">No Tour Reservations</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </center>

    <script>
        window.addEventListener('load', () => {
            let url_string = location.href;
            let url = new URL(url_string);
            var action = url.searchParams.get("action");
            if(action == "Print") {
                window.print();
            }else {
                console.log(true);
                var data = [];
                var rows = document.querySelectorAll(".datatable tr");
    
                for (var i = 0; i < rows.length; i++) {
                    var row = [], cols = rows[i].querySelectorAll("td, th");
                    for (var j = 0; j < cols.length; j++) {
                            let col = cols[j].innerText.replace(/,|\n/g, " ")
                            row.push(col);
                    }
                    data.push(row.join(","));
                }
    
                downloadCSVFile(data.join("\n"), 'booking_report');
                window.close();
            }
        });

        function downloadCSVFile(csv, filename) {
            var csv_file, download_link;

            csv_file = new Blob([csv], {type: "text/csv"});

            download_link = document.createElement("a");

            download_link.download = filename;

            download_link.href = window.URL.createObjectURL(csv_file);

            download_link.style.display = "none";

            document.body.appendChild(download_link);

            download_link.click();
        }
    </script>
</body>
</html>