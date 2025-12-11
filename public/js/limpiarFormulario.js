function limpiarFormulario() {
    let msg = document.getElementById('textMsg');
    document.getElementById('form').reset();
    if (msg) {
        msg.textContent = '';
    }
}