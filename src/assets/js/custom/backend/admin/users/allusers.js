import Cruds from "corejs/crud_interface";
import { readurl } from "corejs/profile_img";
import select2 from "corejs/select2_manager";
class AllUsers {
  constructor(element) {
    this.element = element;
    this.selectTag = ["group"];
  }
  _init = () => {
    this._setupVariables();
    this._setupEvents();
  };
  _setupVariables = () => {
    this.wrapper = this.element.find("#allusers-wrapper");
    this.modalbox = this.element.find("#modal-box");
    this.modalform = this.element.find("#user-frm");
  };
  _setupEvents = () => {
    var phpPlugin = this;
    const csrftoken = document.querySelector('meta[name="csrftoken"]');
    phpPlugin.modalbox
      .find(".form-text")
      .css(
        "font-size",
        phpPlugin.modalbox.find(".upload-box").width() * 0.16 * 0.9
      );

    /**
     * Init Crud Operations
     * ==========================================================================
     */
    let cruds = new Cruds({
      table: "users",
      wrapper: phpPlugin.wrapper,
      form: phpPlugin.modalform,
      modal: phpPlugin.modalbox,
      bsmodal: "modal-box",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_users_page",
      select_tag: phpPlugin.selectTag,
    });
    /**
     * Select 2 Categories initialization
     * ====================================================================
     */
    new select2()._init({
      url: "showDetails",
      element: phpPlugin.modalform.find(".group"),
      tbl_options: "groups",
      placeholder: "Select a user",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
    });

    /**
     * Add or Update post
     * ======================================================================
     */
    phpPlugin.modalbox.on("submit", "#user-frm", function (e) {
      e.preventDefault();
      phpPlugin.modalform.find("#submitBtn").val("Please wait...");
      cruds._add_update({
        datatable: false,
        swal: true,
        modal: true,
        media: phpPlugin.modalbox.find("#p_media"),
        data_type: "values",
        validator_rules: "backend_users",
        frm: $(this),
        frm_name: $(this).attr("id"),
        folder: "users",
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
          "userID",
          "date_enreg",
          "updateAt",
          "status",
          "deleted",
          "firstName",
          "lastName",
          "userName",
          "email",
          "phone",
          "group",
          "userName",
          "email",
          "phone",
          "profileImage",
        ],
        tbl_options: "groups",
        table: "users",
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
      upload_img: ".upload-box .img",
      inputHidden: ["userID", "operation", "date_enreg", "updateAt", "deleted"],
    });
    /**
     * Delete
     * =====================================================================
     */
    cruds._delete({
      swal: true,
      datatable: false,
      url_check: "checkdelete",
      delete_frm_class: ".delete_user",
      frm: true,
      folder: "users",
      del_method: "delete_user",
    });

    /**
     * Restore Users
     * =====================================================================
     */
    cruds._restore({
      swal: true,
      datatable: false,
      restore_frm_class: ".restore_user",
      swal_button: "Restore",
      frm: true,
      swal_message: "You want to restore this user",
      del_method: "restore_user",
      url_check: "checkdelete",
    });
    //Activate item
    cruds._active_inactive_elmt({ table: "categories" });
    // Upload profile

    phpPlugin.modalbox
      .find('.upload-box input[type="file"]')
      .on("change", function () {
        readurl(
          this,
          phpPlugin.modalbox.find(".upload-box .img"),
          phpPlugin.modalbox.find(".upload-box .camera-icon")
        );
      });
  };
}
document.addEventListener("DOMContentLoaded", () => {
  new AllUsers($(".page-container"))._init();
});
