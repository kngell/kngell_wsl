import Cruds from "corejs/crud_interface";
class AllCompanies {
  constructor(element) {
    this.element = element;
  }
  _init = () => {
    this._setupVariables();
    this._setupEvents();
  };
  _setupVariables = () => {
    this.wrapper = this.element.find("#company-wrapper");
    this.modalbox = this.element.find("#modal-box");
    this.modalform = this.element.find("#modal-box #company-frm");
  };
  _setupEvents = () => {
    var phpPlugin = this;
    const csrftoken = document.querySelector('meta[name="csrftoken"]');
    /**
     * Init Cruds operations
     * =======================================================================
     */
    let cruds = new Cruds({
      table: "company",
      wrapper: phpPlugin.wrapper,
      form: phpPlugin.modalform,
      modal: phpPlugin.modalbox,
      bsmodal: "modal-box",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_company_page",
    });

    /**
     * display all Items
     * =======================================================================
     */
    cruds._displayAll({
      datatable: true,
      data_type: "values",
    });

    /**
     * Add or Update post
     * ======================================================================
     */
    phpPlugin.modalbox.on("submit", "#company-frm", function (e) {
      e.preventDefault();
      phpPlugin.modalform.find("#submitBtn").val("Please wait...");
      cruds._add_update({
        frm_name: "company-frm",
        datatable: true,
        swal: true,
        modal: true,
        csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
        frm_name: "all_product_page", // page csrf name
        data_type: "values",
        frm: $(this),
        frm_name: $(this).attr("id"),
      });
    });
    /**
     * Add New
     * =====================================================
     */
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
          "compID",
          "sigle",
          "denomination",
          "siret",
          "site_web",
          "created_at",
          "updated_at",
          "rna",
          "tva",
          "activite",
          "couriel",
          "phone",
          "mobile",
          "fax",
          "address1",
          "zip_code",
          "ville",
          "pays",
          "created_at",
          "updated_at",
          "deleted",
        ],
        table: "company",
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
      inputHidden: [
        "compID",
        "created_at",
        "updated_at",
        "deleted",
        "operation",
      ],
    });

    /**
     * Delete
     * =====================================================================
     */
    cruds._delete({
      swal: true,
      datatable: true,
      url_check: "checkdelete",
      delete_frm_class: ".delete-company-frm",
      data_type: "values",
      frm: true,
    });

    /**
     * Active Elt
     * ======================================================================
     */
    cruds._active_inactive_elmt({ table: "company" });
  };
}
document.addEventListener("DOMContentLoaded", () => {
  new AllCompanies($(".page-content"))._init();
});
