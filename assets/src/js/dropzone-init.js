import Dropzone from "dropzone";

Dropzone.autoDiscover = false;

window.dropzoneInstances = {}; // Global object to store instances by selector

// Select all dropzone elements
document.querySelectorAll(".dropzone").forEach((element, index) => {
  const dz = new Dropzone(element, {
    url: "#", // placeholder, actual upload is handled by AJAX in form
    maxFilesize: 2,
    acceptedFiles: "image/*",
    addRemoveLinks: true,
    uploadMultiple: true,
    parallelUploads: 10,
    maxFiles: 10,

    // Hungarian translations
    dictDefaultMessage: localize.translations.dropzone.defaultMessage,
    dictFallbackMessage: localize.translations.dropzone.fallbackMessage,
    dictFileTooBig: localize.translations.dropzone.fileTooBig,
    dictInvalidFileType: localize.translations.dropzone.invalidFileType,
    dictResponseError: localize.translations.dropzone.responseError,
    dictCancelUpload: localize.translations.dropzone.cancelUpload,
    dictCancelUploadConfirmation: localize.translations.dropzone.cancelUploadConfirmation,
    dictRemoveFile: localize.translations.dropzone.removeFile,
    dictMaxFilesExceeded: localize.translations.dropzone.maxFilesExceeded,

    clickable: true
  });

  dz.on("addedfile", file => {
    //console.log(`Dropzone ${index}: File added`, file.name);
  });

  dz.on("successmultiple", (files, response) => {
    //console.log(`Dropzone ${index}: Upload success`, response);
  });

  dz.on("errormultiple", (files, errorMessage) => {
    console.error(`Dropzone ${index}: Upload error`, errorMessage);
  });

  // Save instance globally using the element ID as key
  if (element.id) {
    window.dropzoneInstances[element.id] = dz;
  }
});
