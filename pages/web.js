function PostFunction(name, prms = '') {
    var postForm = document.getElementById("postForm");
    postForm.page.value = name;
    postForm.prms.value = prms;
    postForm.submit();
}
