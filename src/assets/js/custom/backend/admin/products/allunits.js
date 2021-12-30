import Cruds from "corejs/crud_interface";
import select2 from "corejs/select2_manager";
class AllUnits {
  constructor(element) {
    this.element = element;
    this.selectTag = ["unit"];
  }
  _init = () => {
    this._setupVariables();
    this._setupEvents();
  };
  _setupVariables = () => {
    this.wrapper = this.element.find(".card");
    this.modalbox = this.element.find("#modal-box");
    this.modalform = this.element.find("#units-frm");
  };
  _setupEvents = () => {
    var phpPlugin = this;
    const csrftoken = document.querySelector('meta[name="csrftoken"]');
    /**
     * Init Crud Operations
     * ==========================================================================
     */
    let cruds = new Cruds({
      table: "units",
      wrapper: phpPlugin.wrapper,
      form: phpPlugin.modalform,
      modal: phpPlugin.modalbox,
      bsmodal: "modal-box",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_unit_page",
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
     * Select 2 Unit initialization
     * ====================================================================
     */
    let myselect2 = new select2();
    myselect2._init({
      url: "showDetails",
      element: phpPlugin.modalform.find(".unit"),
      tbl_options: "units",
      placeholder: "Please select a unit",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
    });

    /**
     * Add or Update
     * =======================================================================
     */
    phpPlugin.modalbox.on("submit", "#units-frm", function (e) {
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
    });

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
          "unID",
          "unit",
          "created_at",
          "updated_at",
          "descr",
          "status",
        ],
        tbl_options: "units",
        table: "units",
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
      inputHidden: ["unID", "created_at", "updated_at", "deleted", "operation"],
    });

    /**
     * Delete
     * =====================================================================
     */
    cruds._delete({
      swal: true,
      datatable: true,
      url_check: "checkdelete",
      delete_frm_class: ".delete-unit-status",
      data_type: "values",
      frm: true,
    });

    /**
     * Manage Status
     * ====================================================================
     */
    cruds._active_inactive_elmt({ table: "units" });
  };
}
document.addEventListener("DOMContentLoaded", () => {
  new AllUnits($(".page-container"))._init();
});
