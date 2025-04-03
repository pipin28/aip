function searchDepartments() {
    let input = document.getElementById("searchBar").value.toLowerCase();
    let table = document.getElementById("sectorTable");
    let rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) { // Skipping the header row
        let cells = rows[i].getElementsByTagName("td");
        if (cells.length > 1) {  // Ensuring it's a row with valid data
            let sector_name = cells[1].textContent.toLowerCase();  // Adjusted for the correct column (2nd column)
            if (sector_name.includes(input)) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
}
