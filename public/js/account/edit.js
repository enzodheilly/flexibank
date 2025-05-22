function showLoadingBeforeSubmit(event) {
  event.preventDefault();
  document.getElementById('loading-overlay').style.display = 'flex';
  event.target.submit();
  return false;
}
