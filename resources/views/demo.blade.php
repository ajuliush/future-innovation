<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Hello, world!</title>
    <style>
        #files-area {
            width: 30%;
            margin: 0 auto;
        }

        .file-block {
            border-radius: 10px;
            background-color: rgba(144, 163, 203, 0.2);
            margin: 5px;
            color: initial;
            display: inline-flex;

            &>span.name {
                padding-right: 10px;
                width: max-content;
                display: inline-flex;
            }
        }

        .file-delete {
            display: flex;
            width: 24px;
            color: initial;
            background-color: #6eb4ff00;
            font-size: large;
            justify-content: center;
            margin-right: 3px;
            cursor: pointer;

            &:hover {
                background-color: rgba(144, 163, 203, 0.2);
                border-radius: 10px;
            }

            &>span {
                transform: rotate(45deg);
            }
        }

        #docx, #excel{
            max-height: 500px;
            /* Set a maximum height for scrolling */
            overflow-y: auto;
            /* Enable vertical scrolling if content exceeds the maximum height */
            /* border: 1px solid #ccc; */
            /* Add a border for visual appeal */
            padding: 10px;
            /* Add some padding for better appearance */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                @if (session('success'))
                    <p>{{ session('success') }}</p>
                @endif
                <div class="row">
                    <div class="col-md-3">
                        <p class="mt-5 text-center">
                        <form action="{{ route('store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <label for="attachment">
                                <a class="btn btn-primary text-light" role="button" aria-disabled="false">+ Add</a>
                            </label>
                            <input type="file" name="files[]" accept=".docx,.pdf,.xlsx" id="attachment"
                                class="form-control" style="visibility: hidden; position: absolute;" multiple />

                            </p>
                    </div>
                    <div class="col-md-3">
                        <p class="mt-5 text-center" id="files-area">
                            <span id="filesList">
                                <span id="files-names"></span>
                            </span>
                        </p>

                        <button type="submit" class="btn btn-info">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label for="">Preview</label>
                <div id="pdf"></div>
                <div id="docx"></div>
                <div id="excel"></div>
            </div>
            <div class="col-md-6">
                <table class="table">
                    <thead>
                        <tr>
                            @foreach ($files as $item)
                                <th
                                    class="py-2 px-4 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold border-b">
                                    <a href="javascript:void(0)" class="btn btn-info show" data-toggle="tooltip"
                                        title="{{ $item->filename }}" data-id="{{ $item->id }}">
                                        Show</a>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mammoth@1.4.8/mammoth.browser.min.js"></script>
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
    <script>
        const dt = new DataTransfer(); // Permet de manipuler les fichiers de l'input file

        $("#attachment").on('change', function(e) {
            for (var i = 0; i < this.files.length; i++) {
                let fileBloc = $('<span/>', {
                        class: 'file-block'
                    }),
                    fileName = $('<span/>', {
                        class: 'name',
                        text: this.files.item(i).name
                    });
                fileBloc.append('<span class="file-delete"><span>+</span></span>')
                    .append(fileName);
                $("#filesList > #files-names").append(fileBloc);
            };
            // Ajout des fichiers dans l'objet DataTransfer
            for (let file of this.files) {
                dt.items.add(file);
            }
            // Mise à jour des fichiers de l'input file après ajout
            this.files = dt.files;

            // EventListener pour le bouton de suppression créé
            $('span.file-delete').click(function() {
                let name = $(this).next('span.name').text();
                // Supprimer l'affichage du nom de fichier
                $(this).parent().remove();
                for (let i = 0; i < dt.items.length; i++) {
                    // Correspondance du fichier et du nom
                    if (name === dt.items[i].getAsFile().name) {
                        // Suppression du fichier dans l'objet DataTransfer
                        dt.items.remove(i);
                        continue;
                    }
                }
                // Mise à jour des fichiers de l'input file après suppression
                document.getElementById('attachment').files = dt.files;
            });
        });
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            //value retriving and opening the edit modal starts
            $('.show').on('click', function() {
                var id = $(this).data('id');
                // alert(id);
                $.ajax({
                    type: "POST",
                    url: 'file-by-id',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(res) {
                        var pdfUrl = res.path; // Assuming res.path is the URL of the PDF file
                        // Extract the file extension from the URL
                        var fileExtension = pdfUrl.split('.').pop().toLowerCase();
                        if (fileExtension === 'pdf') {
                            // For PDF files, create an embed tag and display it
                            $('#docx').hide();
                            $('#excel').hide();
                            $('#pdf').show();
                            var pdfUrl = res.path;
                            var embedTag = '<embed src="' + pdfUrl +
                                '" width="500px" height="600px" />';
                            $('#pdf').html(embedTag);
                        } else if (fileExtension === 'docx' || fileExtension === 'doc') {
                            var docx = res.filename;
                            console.log(docx);
                            previewUploadedDocx(docx);
                            $('#pdf').hide();
                            $('#excel').hide();
                            $('#docx').show();
                        } else if (fileExtension === 'xlsx') {
                            var xlsx = res.filename;
                            console.log(xlsx);
                            previewUploadedExcel(xlsx);
                            $('#pdf').hide();
                            $('#docx').hide();
                            $('#excel').show();
                        } else {
                            // For other file types, you can handle them similarly or customize as needed
                            console.log("Unsupported file type: " + fileExtension);
                        }
                    }
                });
            });
        });

        function previewUploadedDocx(docx) {
            // Replace 'your-docx-file-url' with the actual URL of your uploaded DOCX file
            var docxFileUrl = '{{ asset('uploads') }}/' + docx;

            // Fetch the DOCX file from the server
            fetch(docxFileUrl)
                .then(response => response.arrayBuffer())
                .then(arrayBuffer => {
                    // Process the arrayBuffer using mammoth
                    return mammoth.convertToHtml({
                        arrayBuffer: arrayBuffer
                    });
                })
                .then(resultObject => {
                    // Display the result in the specified div
                    var resultElement = document.getElementById("docx");
                    resultElement.innerHTML = resultObject.value;
                    console.log(resultObject.value);
                })
                .catch(error => {
                    console.error("Error fetching or converting DOCX:", error);
                });
        }

        function previewUploadedExcel(xlsx) {
            var excelFileUrl = '{{ asset('uploads') }}/' + xlsx;


            let viewer = document.getElementById('excel');
            let workBook = null;
            let excelGrid = null;
            let activeSheet = '';
            let sheets = [];
            let excelButtons = null;
            let buttons = [];

            function showSheet(el) {
                let buttons = document.querySelectorAll('button');
                buttons.forEach((button) => {
                    button.classList.remove('active');
                });

                el.classList.add('active');
                let workSheet = workBook.Sheets[el.innerText];
                excelGrid.innerHTML = XLSX.utils.sheet_to_html(workSheet);
                activeSheet = el.innerText;
            }

            function clearAll() {
                viewer.innerHTML = '';
                workBook = null;
                excelGrid = null;
                sheets = [];
                excelButtons = null;
                buttons = [];
            }

            function loadExcelFile() {
                clearAll();

                fetch(excelFileUrl)
                    .then((response) => response.arrayBuffer())
                    .then((data) => {
                        let arr = new Uint8Array(data);
                        let workbook = XLSX.read(arr, {
                            type: 'array'
                        });
                        workBook = workbook;
                        sheets = workbook.SheetNames;

                        sheets.forEach((sheetName) => {
                            let sheet = workbook.Sheets[sheetName];
                            let range = XLSX.utils.decode_range(sheet['!ref']);
                            let headers = [];

                            for (let C = range.s.c; C <= range.e.c; ++C) {
                                let cell = sheet[XLSX.utils.encode_cell({
                                    c: C,
                                    r: range.s.r
                                })];
                                let hdr = 'NIPUN';

                                if (cell && cell.t) {
                                    hdr = XLSX.utils.format_cell(cell);
                                }

                                headers.push(hdr);
                            }

                            let roa = XLSX.utils.sheet_to_json(sheet);

                            if (roa.length > 0) {
                                roa.forEach((row) => {
                                    headers.forEach((hd) => {
                                        if (row[hd] === undefined) {
                                            row[hd] = '';
                                        }
                                    });
                                });
                            }
                        });

                        excelGrid = document.createElement('table');
                        excelGrid.classList.add('table');
                        excelGrid.classList.add('table-bordered');
                        excelGrid.classList.add('table-responsive');
                        excelGrid.classList.add('excel-table');

                        excelButtons = document.createElement('div');
                        excelButtons.classList.add('excelButtons');

                        sheets.forEach((sheetName) => {
                            let button = document.createElement('button');
                            button.classList.add('sheetBtn');
                            button.innerText = sheetName;
                            button.addEventListener('click', (e) => {
                                showSheet(e.target);
                            });

                            excelButtons.appendChild(button);
                            buttons.push(button);
                        });

                        let container = document.createElement('div');
                        container.classList.add('excel-container');
                        container.appendChild(excelGrid);

                        viewer.innerHTML = '';
                        viewer.appendChild(container);
                        viewer.appendChild(excelButtons);

                        showSheet(buttons[0]);
                    })
                    .catch((error) => {
                        console.log('Error loading Excel file:', error);
                    });
            }

            loadExcelFile();
        }
    </script>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    {{-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script> --}}
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>
