<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJAX Laravel CRUD</title>
    <link href="{{ asset('images/images.png') }}" rel="icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container mt-5">
        <h2>AJAX Laravel CRUD</h2>
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#crudModal">Add Record</button>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Number</th>
                    <th>Email</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="recordsTable">
                <!-- Records will be displayed here -->
            </tbody>
        </table>
    </div>

    <!-- CRUD Modal -->
    <div class="modal fade" id="crudModal" tabindex="-1" role="dialog" aria-labelledby="crudModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="crudForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="crudModalLabel">Add Record</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="record_id">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="number">Number</label>
                            <input type="text" name="number" class="form-control" id="number" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" name="image" id="image">
                            <img id="image-preview" src="" style="max-width: 100%; display: none;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            fetchRecords();

            function fetchRecords() {
                $.ajax({
                    url: "/records",
                    method: "GET",
                    success: function(response) {
                        let records = '';
                        response.forEach(record => {
                            records += `
                                <tr>
                                    <td>${record.name}</td>
                                    <td>${record.number}</td>
                                    <td>${record.email}</td>
                                    <td>${record.image ? `<img src="images/${record.image}" width="50">` : ''}</td>
                                    <td>${record.description}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm editBtn" data-id="${record.id}">Edit</button>
                                        <button class="btn btn-danger btn-sm deleteBtn" data-id="${record.id}">Delete</button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#recordsTable').html(records);
                    }
                });
            }

            $('#saveBtn').click(function() {
                let name = $('#name').val();
                localStorage.setItem('record_name', name);

                let formData = new FormData($('#crudForm')[0]);
                let id = $('#record_id').val();
                let url = id ? `/records/${id}` : '/records';
                let method = id ? 'PUT' : 'POST';

                formData.append('_method', method);

                $.ajax({
                    url: url,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#crudModal').modal('hide');
                        fetchRecords();
                        Swal.fire('Success!', response.success, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseText, 'error');
                    }
                });
            });

            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: `/records/${id}`,
                    method: "GET",
                    success: function(response) {
                        $('#record_id').val(response.id);
                        $('#name').val(response.name);
                        $('#number').val(response.number);
                        $('#email').val(response.email);
                        $('#description').val(response.description);
                                    // Display the image
                        if (response.image) {
                            let imageUrl = `images/${response.image}`; // Adjust the URL path as per your storage configuration
                            $('#image-preview').attr('src', imageUrl).show();
                        } else {
                            $('#image-preview').attr('src', '').hide(); // Hide image preview if no image is available
                        }
                        $('#crudModal').modal('show');
                    }
                });
            });

            $(document).on('click', '.deleteBtn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/records/${id}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                fetchRecords();
                                Swal.fire('Deleted!', response.success, 'success');
                            }
                        });
                    }
                });
            });

            $('#crudModal').on('hidden.bs.modal', function() {
                $('#crudForm')[0].reset();
                $('#record_id').val('');
            });
        });
    </script>
</body>
</html>
