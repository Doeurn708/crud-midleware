const API = "../api/category/";

function loadCategories() {
  $.ajax({
    url: API + "get.php",
    method: "GET",
    dataType: "json",
    success: function (res) {
      const $tbody = $("#categoryTable");
      $tbody.empty();

      if (!res.success || res.data.length === 0) {
        $tbody.append(
          '<tr><td colspan = "5" style="text-align:center; color:#999">No category</td></tr>',
        );
        return;
      }
      $.each(res.data, function (index, cat) {
        $tbody.append(
          "<tr>" +
            "<td>" +
            (index + 1) +
            "</td>" +
            "<td>" +
            cat.name +
            "</td>" +
            "<td>" +
            (cat.description ?? "") +
            "</td>" +
            "<td>" +
            cat.created_at +
            "</td>" +
            "<td>" +
            '<button class="btn btn-secondary btn-sm btn-edit" ' +
            'data-id="' +
            cat.id +
            '" ' +
            'data-name="' +
            cat.name +
            '" ' +
            'data-description="' +
            (cat.description ?? "") +
            '">Edit</button>' +
            '<button class="btn btn-danger btn-sm btn-delete" data-id="' +
            cat.id +
            '">Delete</button>' +
            "</td>" +
            "</tr>",
        );
      });
    },
  });
}

$('#btnAdd').on('click', function () {
    $('#categoryForm')[0].reset();
    $('#categoryId').val('');
    $('#modalTitle').text('Add Category');
    $('#modal').addClass('show');
});

$('#btnCancel').on('click', function () {
    $('#modal').removeClass('show');
})

