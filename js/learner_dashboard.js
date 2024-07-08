document.addEventListener("DOMContentLoaded", function() {
    const statusCounts = JSON.parse(document.getElementById('status-counts').textContent);
    const statusFilter = document.getElementById("status-filter");
    const courseBoxes = Array.from(document.querySelectorAll(".course-box-container"));
    const coursesContainer = document.getElementById("courses-container");
    const courseSearchInput = document.getElementById("course-search");
    const noResultsMessage = document.getElementById("no-results-message");

    function filterCourses() {
        const status = statusFilter.value;
        const searchQuery = courseSearchInput.value.trim().toLowerCase();

        // Remove only the course boxes, not the no-results message
        courseBoxes.forEach(box => box.remove());
        noResultsMessage.style.display = "none";

        // Clear the container
        coursesContainer.innerHTML = "";
        noResultsMessage.style.display = "none";

        // Filter course boxes based on selected status and search query
        const filteredBoxes = courseBoxes.filter(box => {
            const courseName = box.querySelector(".course-header p").textContent.toLowerCase();
            const statusMatch = status === "all" || box.getAttribute("data-status") === status;
            const searchMatch = searchQuery === "" || courseName.includes(searchQuery);
            return statusMatch && searchMatch;
        });

        // If no results found, display no results message and sad panda image
        if (filteredBoxes.length === 0) {
            noResultsMessage.style.display = "block";
        }

        // Split filtered boxes into chunks of 5
        const chunks = [];
        for (let i = 0; i < filteredBoxes.length; i += 5) {
            chunks.push(filteredBoxes.slice(i, i + 5));
        }

        // Append filtered and chunked boxes to the container
        chunks.forEach(chunk => {
            const row = document.createElement("div");
            row.className = "row course-row";
            chunk.forEach(box => {
                const clonedBox = box.cloneNode(true); // Clone the box to keep it for future filters
                row.appendChild(clonedBox);
            });
            coursesContainer.appendChild(row);
        });
    }

    // Initial filter
    filterCourses();

    // Event listeners
    statusFilter.addEventListener("change", function() {
        filterCourses();
    });

    courseSearchInput.addEventListener("input", function() {
        filterCourses();
    });

    // Function to update filter counts
    function updateFilterCounts(statusCounts) {
        document.querySelector('option[value="all"]').textContent = `All (${statusCounts['all']})`;
        document.querySelector('option[value="failed"]').textContent = `Failed (${statusCounts['failed']})`;
        document.querySelector('option[value="passed"]').textContent = `Passed (${statusCounts['passed']})`;
        document.querySelector('option[value="completed"]').textContent = `Completed (${statusCounts['completed']})`;
        document.querySelector('option[value="not_started"]').textContent = `Not Started (${statusCounts['not_started']})`;
        document.querySelector('option[value="in_progress"]').textContent = `In Progress (${statusCounts['in_progress']})`;
    }

    // Update the counts in the filter options
    updateFilterCounts(statusCounts);
});
