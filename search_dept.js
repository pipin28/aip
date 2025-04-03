function searchDept() {
    let input = document.getElementById("searchBar").value.toLowerCase();
    let table = document.getElementById("sectorTable");
    let rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) { // Skipping the header row
        let cells = rows[i].getElementsByTagName("td");
        let department_office = cells[1].textContent.toLowerCase();
        let department_init = cells[2].textContent.toLowerCase();
        // let sector_category = cells[3].textContent.toLowerCase();
        

        if (department_office.includes(input) || department_init.includes(input)) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
}

