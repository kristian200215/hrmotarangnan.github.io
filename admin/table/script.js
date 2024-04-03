$(document).ready(function() {
    $.getJSON('fetch_data.php', function(data) {
        let employees = {};

        $.each(data, function(key, value) {
            if (!employees[value.employee_id]) {
                employees[value.employee_id] = {
                    name: value.first_name + ' ' + value.last_name,
                    records: []
                };
            }
            employees[value.employee_id].records.push({
                position_title: value.position_title,
                start_date: value.start_date,
                end_date: value.end_date ? value.end_date : 'Present',
                status: value.status
            });
        });

        $.each(employees, function(id, employee) {
            employee.records.sort(function(a, b) {
                return new Date(b.start_date) - new Date(a.start_date);
            });

            let firstRecord = true;
            $.each(employee.records, function(index, record) {
                let statusClass = '';
                let iconHtml = '';
                switch(record.status) {
                    case 'Transferred': statusClass = 'status-transferred'; iconHtml = '<i class="fas fa-exchange-alt"></i> '; break;
                    case 'Promoted': statusClass = 'status-promoted'; iconHtml = '<i class="fas fa-arrow-up"></i> '; break;
                    case 'Retired': statusClass = 'status-retired'; iconHtml = '<i class="fas fa-retirement"></i> '; break;
                    case 'Active': statusClass = 'status-active'; iconHtml = '<i class="fas fa-check"></i> '; break;
                    case 'Resigned': statusClass = 'status-resigned'; iconHtml = '<i class="fas fa-sign-out-alt"></i> '; break;
                    case 'End Of Term': statusClass = 'status-endofterm'; iconHtml = '<i class="fas fa-sign-out-alt"></i> '; break;
                }
                let statusHtml = `<span class="status-badge ${statusClass}">${iconHtml}${record.status}</span>`;
                
                // Only add action buttons for the first record
                let actionButtons = '';
                if(firstRecord) {
                    actionButtons += `<a href="#" class="btn-simple delete-btn" data-employee-id="${id}">Delete</a>`;
                    actionButtons += `<a href="view_service_record.php?employee_id=${id}" class="btn-simple">View</a>`;
                    actionButtons += `<a href="#" class="btn-simple toggle-history" data-employee-id="${id}">View History</a>`;
                }
                
                let hiddenClass = index > 0 ? 'hidden-record' : '';
                let rows = `<tr class="${hiddenClass} employee-${id}" data-employee-id="${id}">
                                <td>${firstRecord ? employee.name : ''}</td>
                                <td>${record.position_title}</td>
                                <td>${record.start_date}</td>
                                <td>${record.end_date}</td>
                                <td>${statusHtml}</td>
                                <td>${actionButtons}</td>
                            </tr>`;
                $('#employeeData').append(rows);
                firstRecord = false;
            });

            if(employee.records.length > 1) {
                $(`.employee-${id}:not(:first)`).hide();
            }
        });

        // Delete button event
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            if(confirm("Are you sure you want to delete this record?")) {
                let employeeId = $(this).data('employeeId'); // Corrected data attribute access
        
                // AJAX request to backend PHP script for deletion
                $.ajax({
                    url: 'delete_employee.php', // Path to your PHP script
                    type: 'POST',
                    data: { employee_id: employeeId },
                    success: function(response) {
                        // Parse the JSON response from the PHP script
                        let result = JSON.parse(response);
                        if(result.success) {
                            // Remove the employee rows from the table if deletion was successful
                            $(`.employee-${employeeId}`).remove();
                        } else {
                            // Handle failure (optional)
                            alert("An error occurred. Please try again.");
                        }
                    },
                    error: function() {
                        // Handle AJAX error (optional)
                        alert("An error occurred with the AJAX request. Please try again.");
                    }
                });
            }
        });
        
        

        $(document).on('click', '.toggle-history', function(e) {
            e.preventDefault();
            let employeeId = $(this).data('employee-id');
            $(`.employee-${employeeId}:not(:first)`).toggle();
        });
    });

    $("#searchBar").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#employeeData tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
