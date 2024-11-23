function menu(dat = null, formFieldsBase64, subDropdownData, pagefrom) {
    // Parse base64 encoded formFields
    let formFields;
    try {
        formFields = JSON.parse(atob(formFieldsBase64));
    } catch (e) {
        console.error("Failed to decode and parse formFields:", e);
        return;
    }
    const data = dat ? JSON.parse(atob(dat)) : null;

    // Generate unique IDs based on the provided data
    const modalId = `exampleModal${data ? data.id : ""}`;
    const saveBtnId = `saveBtn${data ? data.id : ""}`;
    const formId = `menuForm${data ? data.id : ""}`;

    // Remove any existing modal with the same ID
    $("#" + modalId).remove();

    // Generate the modal HTML
    const html = makeForm(
        modalId,
        formId,
        data,
        saveBtnId,
        formFields,
        pagefrom,
        subDropdownData
    );

    // Append the generated HTML to the body and show the modal
    $("body").append(html);
    new bootstrap.Modal(document.getElementById(modalId)).show();

    // Handle save button click event
    $(`#${saveBtnId}`).on("click", function () {
        const formData = new FormData($(`#${formId}`)[0]);
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
            url: "/update",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { "X-CSRF-TOKEN": csrfToken },
            beforeSend: () =>
                $(`#${saveBtnId}`).html(
                    '<i class="fas fa-spinner fa-spin"></i> Saving...'
                ),
            success: (response) => {
                // console.log(response);
                $(`#${saveBtnId}`).html("Save changes");
                $("#" + modalId).modal("hide"); // Close modal using Bootstrap API

                if (response.success) {
                    response.action === "Create"
                        ? appendNewRow(
                              response.data,
                              formFieldsBase64,
                              pagefrom
                          )
                        : updateTableRow(
                              response.data,
                              formFieldsBase64,
                              pagefrom
                          );
                    showToast("success", response.message);
                } else {
                    showToast("error", response.message);
                }
            },
        });
    });
}

function decodeFormFields(formFieldsBase64) {
    try {
        return JSON.parse(atob(formFieldsBase64));
    } catch (e) {
        console.error("Failed to decode formFieldsBase64:", e);
        return [];
    }
}
function orderConfirmModal(orderId, pageFrom) {
    const modalId = `orderConfirmModal`;
    const conirmBtnId = `confirmBtn${orderId}`;
    $("body").append(`
    <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="orderConfirmLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Order</h5>
            <button type="button" class="close-btn" data-bs-dismiss="modal"><i class="fas fa-circle-xmark"></i></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to confirm?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="${conirmBtnId}">Confirm Order</button>
          </div>
        </div>
      </div>
    </div>`);

    new bootstrap.Modal(document.getElementById(modalId)).show();

    $(`#${conirmBtnId}`).on("click", function () {
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
            url: `/confirm-order`, // URL to confirm the order
            method: "POST",
            data: {
                id: orderId, // Send only the order ID
                pagefrom: pageFrom,
            },
            headers: { "X-CSRF-TOKEN": csrfToken },
            beforeSend: () => {
                $("#confirmOrderBtn").html(
                    '<i class="fas fa-spinner fa-spin"></i> Confirming...'
                );
            },
            success: (response) => {
                $("#confirmOrderBtn").html("Confirm Order");
                if (response.success) {
                    showToast("success", response.message);
                    window.location.href = "/booking-list"; // Redirect to thank-you page on success
                } else {
                    showToast("error", response.message);
                }
                $(`#${modalId}`).modal("hide");
            },
            error: (xhr) => {
                let errorMessage =
                    xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : "An error occurred. Please try again.";
                showToast("error", errorMessage);
                $(`#${modalId}`).modal("hide");
            },
        });
    });
}
function appendNewRow(data, formFieldsBase64, pagefrom) {
    const formFields = decodeFormFields(formFieldsBase64);

    const rowHtml = `
      <tr id="row-${data.id}">
        ${formFields
            .map((field) => {
                // Extract the value based on field name
                const value = data[field.name] || "";
                //   console.log(value);

                if (field.type === "dropdown") {
                    // Check if options exist and find the label
                    const option = field.options.find(
                        (option) => option.value == value
                    );
                    return `<td>${option ? option.label : ""}</td>`;
                }

                // Handle image path
                if (field.type === "file") {
                    const imageUrl = value
                        ? `http://127.0.0.1:8000/storage/${value}`
                        : "";
                    return `<td>${
                        imageUrl
                            ? `<a href="${imageUrl}" data-fancybox="gallery"><img src="${imageUrl}" alt="Image" class="w-10" /></a>`
                            : ""
                    }</td>`;
                }

                return `<td>${value}</td>`;
            })
            .join("")}
        <td class="action-td">
          <div class="d-flex g-3">
            <a href="javascript:void(0)" onclick="menu('${btoa(
                JSON.stringify(data)
            )}','${formFieldsBase64}',null,'${pagefrom}')" class="eact btn btn-primary"><i class="fa-solid fa-pen"></i></a>
            <a href="javascript:void(0)" class="eact btn btn-danger" onclick="deleteModal('${
                data.id
            }', '${pagefrom}')"><i class="fa-solid fa-trash"></i></a>
          </div>
        </td>
      </tr>`;
    $("tbody").prepend(rowHtml);
}

function updateTableRow(data, formFieldsBase64, pagefrom) {
    const formFields = decodeFormFields(formFieldsBase64);
    const row = $(`#row-${data.id}`);

    formFields.forEach((field) => {
        // Extract the value based on field name
        const value = data[field.name] || "";
        //console.log(value);
        if (field.type === "dropdown") {
            // Find the selected option based on value
            const selectedOption = field.options.find(
                (option) => option.value == value
            );
            row.find(`td:nth-child(${formFields.indexOf(field) + 1})`).html(
                selectedOption ? selectedOption.label : ""
            );
        } else if (field.type === "file") {
            const imageUrl = value
                ? `http://127.0.0.1:8000/storage/${value}`
                : "";
            row.find(`td:nth-child(${formFields.indexOf(field) + 1})`).html(
                imageUrl
                    ? `<a href="${imageUrl}" data-fancybox="gallery"><img src="${imageUrl}" alt="Image" class="w-10" /></a>`
                    : ""
            );
        } else {
            // Set value for other field types
            row.find(`td:nth-child(${formFields.indexOf(field) + 1})`).html(
                value
            );
        }
    });

    // Create the delete and edit buttons
    let deleteHtml = `
        <div class="d-flex g-3">
            <a href="javascript:void(0)" onclick="menu('${btoa(
                JSON.stringify(data)
            )}', '${formFieldsBase64}', null, '${pagefrom}')" class="eact btn btn-primary">
                <i class="fa-solid fa-pen"></i>
            </a>
            <a href="javascript:void(0)" class="eact btn btn-danger" onclick="deleteModal('${
                data.id
            }', '${pagefrom}')">
                <i class="fa-solid fa-trash"></i>
            </a>
        </div>`;

    row.find("td:last").html(deleteHtml);
}

function deleteModal(id, pagefrom) {
    const modalId = `deleteModal${id}`;
    const deleteBtnId = `confirmDeleteBtn${id}`;
    $("body").append(`
    <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Delete</h5>
            <button type="button" class="close-btn" data-bs-dismiss="modal"><i class="fas fa-circle-xmark"></i></button>
          </div>
          <div class="modal-body"><p>Are you sure you want to delete this item?</p></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="${deleteBtnId}">Delete</button>
          </div>
        </div>
      </div>
    </div>`);

    new bootstrap.Modal(document.getElementById(modalId)).show();

    $(`#${deleteBtnId}`).on("click", function () {
        const csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url: `/delete`,
            method: "POST",
            data: { id, pagefrom },
            headers: { "X-CSRF-TOKEN": csrfToken },
            beforeSend: () =>
                $(`#${deleteBtnId}`).html(
                    '<i class="fas fa-spinner fa-spin"></i> Deleting...'
                ),
            success: (response) => {
                //console.log(response);
                $(`#deleteBtn${id}`).html("Delete");
                response.success
                    ? $(`#row-${id}`).remove()
                    : showToast("error", response.message);
                $(`#${modalId}`).modal("hide");
                showToast("success", response.message);
            },
        });
    });
}

function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    return `${date.getFullYear()}-${(date.getMonth() + 1)
        .toString()
        .padStart(2, "0")}-${date.getDate().toString().padStart(2, "0")} ${date
        .getHours()
        .toString()
        .padStart(2, "0")}:${date
        .getMinutes()
        .toString()
        .padStart(2, "0")}:${date.getSeconds().toString().padStart(2, "0")}`;
}

function showToast(type, message) {
    $.toast({
        text: message,
        heading: type === "success" ? "Success" : "Error",
        icon: type,
        showHideTransition: "fade",
        allowToastClose: true,
        hideAfter: 1700,
        position: "mid-center",
        textAlign: "center",
        loaderBg: type === "success" ? "#9EC600" : "#FF5C5C",
    });
}

///////////Third Approach///////////////
// function makeForm(
//     modalId,
//     formId,
//     data,
//     saveBtnId,
//     formFields,
//     pagefrom,
//     subDropdownData
// ) {
//     if (!Array.isArray(formFields)) {
//         console.error("formFields is not an array:", formFields);
//         return;
//     }

//     let formContent = `
//         <div class="row">
//             <div class="col-sm-8">`;

//     formFields.forEach((field) => {
//         let fieldHtml = "";

//         switch (field.type) {
//             case "text":
//                 fieldHtml = `
//                     <div class="form-group">
//                         <label for="${field.id}">${field.label}</label>
//                         <input type="text" id="${field.id}" name="${
//                     field.name
//                 }" class="form-control" value="${data?.[field.name] || ""}">
//                     </div>`;
//                 break;

//             case "textarea":
//                 fieldHtml = `
//                     <div class="form-group">
//                         <label for="${field.id}">${field.label}</label>
//                         <textarea id="${field.id}" name="${
//                     field.name
//                 }" class="form-control">${data?.[field.name] || ""}</textarea>
//                     </div>`;
//                 break;

//             case "file":
//                 fieldHtml = `
//                     <div class="form-group">
//                         <label for="${field.id}">${field.label}</label>
//                         <div id="drop-zone">
//                             <img src="${
//                                 data?.[field.name] ? data[field.name] : ""
//                             }" alt="" class="drag-img" id="uploaded-image-preview">
//                             <strong class="upIcon"><i class="fas fa-images"></i></strong>
//                             <p class="upload-text">Drop file or click to upload</p>
//                             <input type="hidden" id="${field.name}" name="${
//                     field.name
//                 }">
//                             <input type="file" id="myfile" hidden accept="image/*">
//                         </div>
//                     </div>
//                     <div class="modal" id="cropImageModal" tabindex="-1" role="dialog">
//                         <div class="modal-dialog" role="document">
//                             <div class="modal-content">
//                                 <div class="modal-header">
//                                     <h5 class="modal-title">Crop Image</h5>
//                                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
//                                         <span aria-hidden="true">&times;</span>
//                                     </button>
//                                 </div>
//                                 <div class="modal-body">
//                                     <div class="img-container">
//                                         <img id="cropperImage" src="" alt="Image for cropping">
//                                     </div>
//                                 </div>
//                                 <div class="modal-footer">
//                                     <button type="button" class="btn btn-primary" onclick="cropImage()">Crop & Upload</button>
//                                 </div>
//                             </div>
//                         </div>
//                     </div>`;
//                 break;

//             case "dropdown":
//                 let options = field.options
//                     .map(
//                         (option) => `
//                     <option value="${option.value}" ${
//                             data?.[field.name] === option.value
//                                 ? "selected"
//                                 : ""
//                         }>
//                         ${option.label}
//                     </option>`
//                     )
//                     .join("");

//                 fieldHtml = `
//                     <div class="form-group">
//                         <label for="${field.id}">${field.label}</label>
//                         <select id="${field.id}" name="${field.name}" class="form-control">
//                             ${options}
//                         </select>
//                     </div>`;

//                 if (field.subDropdown) {
//                     fieldHtml += `
//                         <div class="form-group">
//                             <label for="${field.subDropdown.id}">${
//                         field.subDropdown.label
//                     }</label>
//                             <select class="form-control" id="${
//                                 field.subDropdown.id
//                             }" name="${field.subDropdown.name}">
//                                 <option value="">Select an option</option>
//                             </select>
//                         </div>
//                         <script>
//                             document.getElementById('${
//                                 field.id
//                             }').addEventListener('change', function() {
//                                 const selectedValue = this.value;
//                                 const subDropdown = document.getElementById('${
//                                     field.subDropdown.id
//                                 }');
//                                 const options = ${JSON.stringify(
//                                     subDropdownData
//                                 )};
//                                 subDropdown.innerHTML = '<option value="">Select an option</option>';
//                                 if (options[selectedValue]) {
//                                     options[selectedValue].forEach(option => {
//                                         const newOption = document.createElement('option');
//                                         newOption.value = option.value;
//                                         newOption.textContent = option.label;
//                                         subDropdown.appendChild(newOption);
//                                     });
//                                 }
//                             });
//                             document.getElementById('${
//                                 field.id
//                             }').dispatchEvent(new Event('change'));
//                         </script>`;
//                 }
//                 break;

//             default:
//                 console.warn(`Unsupported field type: ${field.type}`);
//         }

//         formContent += fieldHtml;
//     });

//     formContent += `
//                 <div class="btn-submit-group mt-5">
//                     <input type="hidden" value="${
//                         data ? "Update" : "Create"
//                     }" name="action">
//                     <input type="hidden" value="${pagefrom}" name="pagefrom">
//                     <button type="button" class="btn btn-danger" onclick="toggleForm()">Cancel</button>
//                     <button type="submit" class="btn btn-primary" id="${saveBtnId}">Submit</button>
//                 </div>
//             </div>
//             <div class="col-sm-4">
//                 <div class="error-instructions">
//                     <h6>Error Resolution Guide for Admins</h6>
//                     <ul>
//                         <li><strong>Check Error Code:</strong> Note the error code displayed.</li>
//                         <li><strong>Open Console:</strong> Right-click on the page, select <em>Inspect</em> > <em>Console</em> to view detailed logs.</li>
//                         <li><strong>Review Logs:</strong> Look for errors or warnings in the console and server logs.</li>
//                         <li><strong>Verify Configurations:</strong> Ensure API keys, server paths, and settings are correct.</li>
//                         <li><strong>Clear Cache:</strong> Clear browser cache to avoid loading old files.</li>
//                         <li><strong>Search Online:</strong> Look up the error code for community solutions.</li>
//                         <li><strong>Update Software:</strong> Make sure all dependencies are up to date.</li>
//                         <li><strong>Contact Support:</strong> Reach out to support if the issue persists.</li>
//                     </ul>
//                 </div>
//             </div>
//         </div>`;

//     const html = `
//     <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
//         <div class="modal-dialog modal-xl modal-dialog-centered">
//             <div class="modal-content">
//                 <div class="modal-header">
//                     <h5 class="modal-title">${
//                         data ? "Edit Item" : "Add New Item"
//                     }</h5>
//                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
//                 </div>
//                 <div class="modal-body">
//                     <form id="${formId}" method="POST" enctype="multipart/form-data">
//                         ${formContent}
//                     </form>
//                 </div>
//             </div>
//         </div>
//     </div>`;
//     return html;
// }

function searchList(element, pagefrom) {
    // Get the search term from the input
    let search = $("#search").val() || null;
    window.location.href = `/${pagefrom}/${search}`;
}
// Main function to create the form
function makeModal(modalId, modalTitle, formContent) {
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog bounceIn modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${modalTitle}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ${formContent}
                    </div>
                </div>
            </div>
        </div>`;
    return modalHtml;
}

function makeForm(
    modalId,
    formId,
    data,
    saveBtnId,
    formFields,
    pagefrom,
    subDropdownData
) {
    if (!Array.isArray(formFields)) {
        console.error("formFields is not an array:", formFields);
        return;
    }

    let formContent = `
        <form id="${formId}" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-8">`;

    formFields.forEach((field) => {
        let fieldHtml = "";

        switch (field.type) {
            case "text":
                fieldHtml = `
                    <div class="form-group">
                        <label for="${field.id}">${field.label}</label>
                        <input type="text" id="${field.id}" name="${
                    field.name
                }" class="form-control" value="${data?.[field.name] || ""}">
                    </div>`;
                break;

            case "textarea":
                fieldHtml = `
                    <div class="form-group">
                        <label for="${field.id}">${field.label}</label>
                        <textarea id="${field.id}" name="${
                    field.name
                }" class="form-control">${data?.[field.name] || ""}</textarea>
                    </div>`;
                break;

            case "file":
                fieldHtml = `
                    <div class="form-group">
                        <label for="${field.id}">${field.label}</label>
                        <div class="drop-zone">
                            <img alt="" class="drag-img uploaded-image-preview">
                            <strong class="upIcon"><i class="fas fa-images"></i></strong>
                            <p class="upload-text">Drop file or click to upload</p>
                            <input type="file" class="myfile" name="${field.name}" hidden accept="image/*">
                        </div>
                    </div>
                    <script>
                        document.querySelectorAll(".drop-zone").forEach((dropZone) => {
                            const inputElement = dropZone.querySelector(".myfile");
                            const img = dropZone.querySelector(".uploaded-image-preview");
                            const p = dropZone.querySelector(".upload-text");
                            const upIcon = dropZone.querySelector(".upIcon");

                            inputElement.addEventListener("change", function(e) {
                                const clickFile = this.files[0];
                                if (clickFile) {
                                    img.style.display = "block";
                                    p.style.display = "none";
                                    upIcon.style.display = 'none';
                                    const reader = new FileReader();
                                    reader.readAsDataURL(clickFile);
                                    reader.onloadend = function() {
                                        img.src = reader.result;
                                        img.alt = clickFile.name;
                                    };
                                }
                            });

                            dropZone.addEventListener("click", () => inputElement.click());

                            dropZone.addEventListener("dragover", (e) => {
                                e.preventDefault();
                            });

                            dropZone.addEventListener("drop", (e) => {
                                e.preventDefault();
                                const file = e.dataTransfer.files[0];
                                if (file) {
                                    const dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(file);
                                    inputElement.files = dataTransfer.files;
                                    img.style.display = "block";
                                    p.style.display = "none";
                                    upIcon.style.display = 'none';
                                    img.src = URL.createObjectURL(file);
                                    img.alt = file.name;
                                }
                            });
                        });
                    </script>`;
                break;

            case "dropdown":
                let options = field.options
                    .map(
                        (option) => `
                    <option value="${option.value}" ${
                            data?.[field.name] === option.value
                                ? "selected"
                                : ""
                        }>
                        ${option.label}
                    </option>`
                    )
                    .join("");

                fieldHtml = `
                    <div class="form-group">
                        <label for="${field.id}">${field.label}</label>
                        <select id="${field.id}" name="${field.name}" class="form-control">
                            ${options}
                        </select>
                    </div>`;

                if (field.subDropdown) {
                    fieldHtml += `
                        <div class="form-group">
                            <label for="${field.subDropdown.id}">${
                        field.subDropdown.label
                    }</label>
                            <select class="form-control" id="${
                                field.subDropdown.id
                            }" name="${field.subDropdown.name}">
                                <option value="">Select an option</option>
                            </select>
                        </div>
                        <script>
                            document.getElementById('${
                                field.id
                            }').addEventListener('change', function() {
                                const selectedValue = this.value;
                                const subDropdown = document.getElementById('${
                                    field.subDropdown.id
                                }');
                                const options = ${JSON.stringify(
                                    subDropdownData
                                )};
                                subDropdown.innerHTML = '<option value="">Select an option</option>';
                                if (options[selectedValue]) {
                                    options[selectedValue].forEach(option => {
                                        const newOption = document.createElement('option');
                                        newOption.value = option.value;
                                        newOption.textContent = option.label;
                                        subDropdown.appendChild(newOption);
                                    });
                                }
                            });
                            document.getElementById('${
                                field.id
                            }').dispatchEvent(new Event('change'));
                        </script>`;
                }
                break;

            default:
                console.warn(`Unsupported field type: ${field.type}`);
        }

        formContent += fieldHtml;
    });

    formContent += `
                <div class="btn-submit-group mt-5">
                    <input type="hidden" value="${
                        data ? "Update" : "Create"
                    }" name="action">
                    <input type="hidden" name="id" value="${data?.id || ""}">
                    <input type="hidden" value="${pagefrom}" name="pagefrom">
                    <button type="button" class="btn btn-danger" onclick="toggleForm()">Cancel</button>
                    <button type="button" class="btn btn-primary" id="${saveBtnId}">Submit</button>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="error-instructions">
                    <h6>Error Resolution Guide for Admins</h6>
                    <ul>
                        <li><strong>Check Error Code:</strong> Note the error code displayed.</li>
                        <li><strong>Open Console:</strong> Right-click on the page, select <em>Inspect</em> > <em>Console</em> to view detailed logs.</li>
                        <li><strong>Review Logs:</strong> Look for errors or warnings in the console and server logs.</li>
                        <li><strong>Verify Configurations:</strong> Ensure API keys, server paths, and settings are correct.</li>
                        <li><strong>Clear Cache:</strong> Clear browser cache to avoid loading old files.</li>
                        <li><strong>Search Online:</strong> Look up the error code for community solutions.</li>
                        <li><strong>Update Software:</strong> Make sure all dependencies are up to date.</li>
                        <li><strong>Contact Support:</strong> Reach out to support if the issue persists.</li>
                    </ul>
                </div>
            </div>
        </div>
    </form>`;

    // Create the modal HTML using the makeModal function
    return makeModal(modalId, data ? "Edit Item" : "Add New Item", formContent);
}
