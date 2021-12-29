import "plugins/flexadmin/js/ecommerce";
import Cruds from "corejs/crud_interface";
import media from "corejs/upload_interface";
import select2 from "corejs/select2_manager";
import editorLoader from "corejs/editorLoad";

class AllPosts {
  constructor(element) {
    this.element = element;
    this.selectTag = ["categorie"];
  }
  _init = () => {
    this._setupVariables();
    this._setupEvents();
  };
  _setupVariables = () => {
    this.wrapper = this.element.find("#allposts-wrapper");
    this.modalbox = this.element.find("#modal-box");
    this.modalform = this.element.find("#modal-box #posts-frm");
    this.editorContent = ["postContent"];
  };
  _setupEvents = () => {
    var phpPlugin = this;
    const csrftoken = document.querySelector('meta[name="csrftoken"]');
    /**
     * Editor Loader
     * ==========================================================================
     */
    const loader = new editorLoader(phpPlugin.editorContent, {
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_posts_page",
    });

    /**
     * Init Crud Operations
     * ==========================================================================
     */
    let cruds = new Cruds({
      table: "posts",
      wrapper: phpPlugin.wrapper,
      form: phpPlugin.modalform,
      modal: phpPlugin.modalbox,
      bsmodal: "modal-box",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_posts_page",
      ck_content: phpPlugin.editorContent,
      loader: loader,
    });
    /**
     * Select 2 Categories initialization
     * ====================================================================
     */
    let myselect2 = new select2();
    myselect2._init({
      url: "showDetails",
      element: phpPlugin.modalform.find(".categorie"),
      tbl_options: "categories",
      placeholder: "Please select a categorie",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
      multiple: true,
    });
    /**
     * Upload Media
     * ======================================================================
     */
    let dropzone = new media({
      dz_element: phpPlugin.modalform.find(".dragAndDrop__wrapper"),
      submitBtn: "submitBtn1",
    })._upload();

    /**
     * Display All Items
     * ==========================================================================
     */

    cruds._displayAll({
      datatable: true,
      data_type: "values",
      model_method: "getAll",
    });

    /**
     * Add or Update post
     * ======================================================================
     */
    phpPlugin.modalbox.on("submit", "#posts-frm", function (e) {
      e.preventDefault();
      phpPlugin.modalform.find("#submitBtn").val("Please wait...");
      cruds._add_update({
        datatable: true,
        swal: true,
        modal: true,
        categorie: phpPlugin.modalbox.find(".categorie"),
        media: phpPlugin.modalbox.find("#p_media"),
        select: phpPlugin.selectTag,
        data_type: "values",
        // model_method: "get_Products",
        frm: $(this),
        frm_name: $(this).attr("id"),
        imageUrlsAry: Array.from(
          new DOMParser()
            .parseFromString(loader.editor.postContent.data.get(), "text/html")
            .querySelectorAll("img")
        ).map((img) => img.getAttribute("src")),
        folder: "posts",
        dropzone: dropzone,
      });
    });

    phpPlugin.wrapper.find("#addNew").on("click", function () {
      phpPlugin.modalform.find("#operation").val("add");
      if (!loader.check()) {
        loader.load().then(() => {
          cruds.__open_modal();
        });
      } else {
        cruds.__open_modal();
      }
    });

    /**
     * Edit form
     * =======================================================================
     */
    phpPlugin.wrapper.on("click", ".editBtn", function (e) {
      e.preventDefault();
      phpPlugin.modalform.find("#operation").val("update");
      const data = {
        std_fields: [
          "postID",
          "postAuthor",
          "postCommentCount",
          "postContent",
          "postDate",
          "postTitle",
          "updateAt",
          "userID",
          "postStatus",
          "deleted",
          "categorie",
          "p_media",
        ],
        tbl_options: ["categories"],
        categorieElement: phpPlugin.modalform.find("#categorie"),
        table: "posts",
        frm: $(this).parents("form").length != 0 ? $(this).parents("form") : "",
        frm_name: $(this).parents("form").attr("id"),
        tag: $(this),
        dropzone: dropzone,
      };
      if (!loader.check()) {
        loader.load().then(() => {
          cruds.__open_modal();
          cruds._edit(data);
        });
      } else {
        cruds.__open_modal();
        cruds._edit(data);
      }
    });

    /**
     * Clean Form and server
     * =====================================================================
     */
    cruds._clean_form({
      select: phpPlugin.selectTag,
      cke: true,
      dropzone: dropzone,
      inputHidden: [
        "postID",
        "postCommentCount",
        "userID",
        "updateAt",
        "deleted",
        "operation",
      ],
    });
    //Clean temporary data
    document
      .querySelector('[data-bs-dismiss="modal"]')
      .addEventListener("click", (e) => {
        e.preventDefault();
        var imgs = Array.from(
          new DOMParser()
            .parseFromString(loader.editor.postContent.data.get(), "text/html")
            .querySelectorAll("img")
        ).map((img) => img.getAttribute("src"));
        const data = {
          table: "post_file_url",
          folder: "posts",
          csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
          frm_name: "all_posts_page",
          url: "cleanTempFiles",
        };
        cruds._cleanTempFiles(data);
      });
    /**
     * Delete
     * =====================================================================
     */
    cruds._delete({
      swal: true,
      datatable: true,
      url_check: "checkdelete",
      delete_frm_class: ".delete-posts-status",
      data_type: "values",
      frm: true,
      folder: "posts",
      method: "deletePosts",
    });
    /**
     * Active Inactive BTN
     * =====================================================================
     */
    cruds._active_inactive_elmt({ table: "posts" });
  };
}
// Dropzone.autoDiscover = false;
document.addEventListener("DOMContentLoaded", () => {
  new AllPosts($(".page-container"))._init();
});
