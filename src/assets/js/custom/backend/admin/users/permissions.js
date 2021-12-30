import Cruds from "corejs/crud_interface";
import select2 from "corejs/select2_manager";
class AllUnits {
  constructor(element) {
    this.element = element;
    this.selectTag = ["parentID"];
  }
  _init = () => {
    this._setupVariables();
    this._setupEvents();
  };
  _setupVariables = () => {
    this.wrapper = this.element.find("#content-box");
    this.modalbox = this.element.find("#modal-box");
    this.modalform = this.element.find("#groups-and-permissions-frm");
  };
  _setupEvents = () => {
    var phpPlugin = this;
    const csrftoken = document.querySelector('meta[name="csrftoken"]');

    /**
     * Init Crud Operations
     * ==========================================================================
     */
    let cruds = new Cruds({
      table: "groups",
      wrapper: phpPlugin.wrapper,
      form: phpPlugin.modalform,
      modal: phpPlugin.modalbox,
      bsmodal: "modal-box",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_permission_page",
      select_tag: phpPlugin.selectTag,
    });

    /**
     * Display All Items
     * ==========================================================================
     */
    cruds._displayAll({
      datatable: true,
      data_type: "values",
    });

    /**
     * Select 2 Categories initialization
     * ====================================================================
     */
    new select2()._init({
      url: "showDetails",
      element: phpPlugin.modalform.find("#parentID"),
      tbl_options: "groups",
      placeholder: "Select Parent Group",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
    });

    /**
     * Add or Update post
     * ======================================================================
     */
    phpPlugin.modalbox.on(
      "submit",
      "#groups-and-permissions-frm",
      function (e) {
        e.preventDefault();
        phpPlugin.modalform.find("#submitBtn").val("Please wait...");
        cruds._add_update({
          datatable: true,
          swal: true,
          modal: true,
          data_type: "values",
          frm: $(this),
          frm_name: $(this).attr("id"),
        });
      }
    );

    phpPlugin.wrapper.find("#addNew").on("click", function () {
      phpPlugin.modalform.find("#operation").val("add");
    });
    /**
     * Edit form
     * =======================================================================
     */
    phpPlugin.wrapper.on("click", ".editBtn", function (e) {
      e.preventDefault();
      phpPlugin.modalform.find("#operation").val("update");
      cruds._edit({
        std_fields: [
          "grID",
          "date_enreg",
          "updateAt",
          "parentID",
          "deleted",
          "name",
          "description",
          "status",
        ],
        tbl_options: ["groups"],
        table: "groups",
        frm: $(this).parents("form").length != 0 ? $(this).parents("form") : "",
        frm_name: $(this).parents("form").attr("id"),
        tag: $(this),
      });
    });

    /**
     * Clean Form and server
     * =====================================================================
     */
    cruds._clean_form({
      select: phpPlugin.selectTag,
      inputHidden: ["operation", "grID", "date_enreg", "updateAt", "deleted"],
    });

    /**
     * Delete
     * =====================================================================
     */
    cruds._delete({
      swal: true,
      datatable: true,
      url_check: "checkdelete",
      delete_frm_class: ".delete-permissions",
      data_type: "values",
      frm: true,
    });

    /**
     * Manage Status
     */
    cruds._active_inactive_elmt({ table: "groups" });
    //=======================================================================
  };
}
document.addEventListener("DOMContentLoaded", () => {
  new AllUnits($(".page-container"))._init();
});
