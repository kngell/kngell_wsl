class Checkout2 {
  constructor(element) {
    this.element = element;
  }

  _init = () => {
    this._setupVariables();
    this._setupEvents();
  };

  _setupVariables = () => {
    this.progressbar = this.element.find(".progressbar");
    this.prevBtns = document.querySelectorAll(".btn-prev");
    this.nextBtns = document.querySelectorAll(".btn-next");
    this.progressLine = document.querySelector("#progress");
    this.formSteps = document.querySelectorAll(".form-step");
    this.progressSteps = document.querySelectorAll(".progress-step");
  };

  _setupEvents = () => {
    var phpPlugin = this;

    let formStepNum = 0;
    phpPlugin.nextBtns.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        formStepNum++;
        updateFromStep();
        updateProgressBar();
      });
    });
    phpPlugin.prevBtns.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        formStepNum--;
        updateFromStep();
        updateProgressBar();
      });
    });
    function updateFromStep() {
      phpPlugin.formSteps.forEach((formStep) => {
        formStep.classList.contains("form-step-active") &&
          formStep.classList.remove("form-step-active");
      });
      phpPlugin.formSteps[formStepNum].classList.add("form-step-active");
    }
    function updateProgressBar() {
      phpPlugin.progressSteps.forEach((progressStep, idx) => {
        if (idx < formStepNum + 1) {
          progressStep.classList.add("progress-step-active");
        } else {
          progressStep.classList.remove("progress-step-active");
        }
      });
      const progressStepActive = document.querySelectorAll(
        ".progress-step-active"
      );
      phpPlugin.progressLine.style.width =
        ((progressStepActive.length - 1) /
          (phpPlugin.progressSteps.length - 1)) *
          100 +
        "%";
      console.log(phpPlugin.progressLine.style.width, progressStepActive);
    }
  };
}
document.addEventListener("DOMContentLoaded", function () {
  new Checkout2($("#main-site"))._init();
});
