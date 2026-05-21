let currentTab = "guideline";

function statusHelp(status){
   return `<span class="badge badge-${status}">${status === "live" ? "Live" : "Draft"}</span>`;
}

function contentCard(data, containerId,countId, isGuideline){
    document.getElementById(countId).textContent = data.length + " entries";
    const content= document.getElementById(containerId);

    if(data.length === 0){
        content.innerHTML = `
        <div class="empty-state">
        <div class="empty-state-icon">📭</div>
        <strong> No found</strong>
        <p> There are no data loaded yet. Please check back later.</p>
        </div>`;
        return;
    }

     content.innerHTML = data.map(entry => `
        <div class="card">
            <div class="card-thumb">${entry.icon ?? "📄"}</div>
            <div class="card-body">
                <div>
                    <div class="card-top">
                        <span class="card-title">${entry.title}</span>
                        ${statusHelp(entry.status)}
                    </div>
                    <div class="card-meta">
                        ${isGuideline ? "Section: " + entry.section : "Category: " + entry.category}
                        &nbsp;·&nbsp; Updated ${entry.updated}
                    </div>
                    <div class="card-preview">${entry.preview}</div>
                </div>
                <div class="card-actions">
                    <button class="btn-view" onclick="openView(${entry.id}, ${isGuideline})">👁 View</button>
                </div>
            </div>
        </div>
    `).join("");
}

/*open view* -- tak buat lagi tunggu php*/

/* ---- go back ---- */
function goBack() {
    document.getElementById("tabs").style.display = "flex";
    document.getElementById("panel-view").classList.remove("active");
    document.getElementById("breadcrumb").style.display = "none";
    document.getElementById("panel-" + currentTab).classList.add("active");
}

/* switch tab */
function switchTab(tab, el) {
    currentTab = tab;
    document.querySelectorAll(".tab").forEach(t => t.classList.remove("active"));
    el.classList.add("active");
    document.getElementById("panel-guideline").classList.remove("active");
    document.getElementById("panel-faq").classList.remove("active");
    document.getElementById("panel-" + tab).classList.add("active");
}

contentCard([], "guideline-cards", "guideline-count", true);
contentCard([], "faq-cards", "faq-count", false);
