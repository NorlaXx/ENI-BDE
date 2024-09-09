class popup {
  constructor() {
    const id = "myPopup";
    this.popup = document.getElementById(id);

    const crossId = "close";
    this.cross = document.getElementById(crossId);

    const contentId = "content";
    this.content = document.getElementById(contentId);

    this.init();
  }

  init() {
    this.cross.addEventListener("click", () => {
      this.close();
    });
  }

  show() {
    this.popup.style.display = "flex";
  }

  close() {
    this.popup.style.display = "none";
  }

  importHTMLComponent(component) {
    this.clearHTMLComponent();
    this.content.appendChild(component);
  }

  clearHTMLComponent() {
    this.content.innerHTML = "";
  }
}
