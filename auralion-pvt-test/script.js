const cohorts = [
  {
    id: "existing-ground",
    label: "Mam gruntowa pompe",
    meta: "Koszty nie sa takie, jak mialy byc",
    tag: "Optymalizacja istniejacego systemu",
    title: "Masz solanka-woda, ale rachunki nadal potrafia zabolec",
    pain: "Nie chcesz wymieniac wszystkiego. Chcesz wiedziec, czy system pracuje jak powinien i czy da sie poprawic jego prace bez kolejnej duzej rewolucji.",
    fit: ["pomiary pracy instalacji i dolnego zrodla", "analiza kosztow energii i taryf", "PVT lub magazyn jako wsparcie tam, gdzie ma to sens"],
    questions: ["Jakie sa temperatury dolnego zrodla w sezonie?", "Kiedy wlacza sie grzalka albo spada COP?", "Czy problemem jest instalacja, sterowanie czy zrodlo energii?"],
  },
  {
    id: "premium-new",
    label: "Nie mam pompy",
    meta: "Chce cos cichego i wyjatkowego",
    tag: "Dom premium / rezydencja",
    title: "Nie chcesz urzadzenia, ktore bedzie slychac i widac przez nastepne 15 lat",
    pain: "Budujesz albo modernizujesz dom, w ktorym technologia ma sluzyc komfortowi, a nie przypominac o sobie szumem, pudlem na elewacji i kompromisem estetycznym.",
    fit: ["pompa bez jednostki zewnetrznej", "dach jako element systemu ciepla i energii", "strefy komfortu, magazyn energii, przygotowanie pod rozbudowe"],
    questions: ["Czy priorytetem jest cisza, wyglad czy koszt miesieczny?", "Czy w domu beda strefy, basen, SPA albo duze zuzycie cieplej wody?", "Jak wyglada dach i miejsce techniczne?"],
  },
  {
    id: "no-boreholes",
    label: "Chce gruntowa, bez odwiertow",
    meta: "Geologia lub formalnosci blokuja temat",
    tag: "Alternatywa dla odwiertow",
    title: "Chcesz stabilnosci gruntowki, ale dzialka mowi: nie tedy droga",
    pain: "Na papierze gruntowa pompa ciepla wygladala najlepiej. W praktyce pojawily sie warunki geologiczne, brak miejsca, decyzje formalne albo koszt odwiertow, ktory psuje caly plan.",
    fit: ["PVT jako dolne zrodlo lub uzupelnienie zrodla", "wariant bez klasycznej jednostki zewnetrznej", "dobor po audycie dachu, hydrauliki i zapotrzebowania"],
    questions: ["Co dokladnie blokuje odwierty?", "Jak duze jest zapotrzebowanie budynku?", "Czy dach moze pracowac jako aktywny element systemu?"],
  },
  {
    id: "no-air",
    label: "Nie chce powietrznej",
    meta: "Halasu, wygladu albo spadkow pracy zima",
    tag: "Bez jednostki zewnetrznej",
    title: "Pompa powietrze-woda odpada, bo nie chcesz kompromisu przy domu",
    pain: "Nie chodzi o sama technologie. Chodzi o to, ze nie chcesz halasu, nawiewu, widocznej jednostki albo obawy, ze w najzimniejsze dni system bedzie pracowal najgorzej wtedy, kiedy najbardziej go potrzebujesz.",
    fit: ["cichy uklad bez jednostki na elewacji", "projekt pod stabilna prace w sezonie", "uczciwa analiza kosztu inwestycji kontra komfort i przewidywalnosc"],
    questions: ["Co najbardziej przeszkadza w pompie powietrznej?", "Czy wazniejsza jest cisza, estetyka czy praca w mrozie?", "Jakie masz alternatywy: gaz, prad, grunt, biomasa?"],
  },
  {
    id: "limited-boreholes",
    label: "Mam limit odwiertow",
    meta: "Chce gruntowa, ale mniej inwazyjnie",
    tag: "Ograniczone dolne zrodlo",
    title: "Dzialka pozwala na czesc rozwiazania, ale nie na tyle, ile potrzeba",
    pain: "Nie chcesz porzucac koncepcji gruntowej pompy. Chcesz sprawdzic, czy da sie ograniczyc liczbe odwiertow albo odciazyc dolne zrodlo, zanim inwestycja zrobi sie zbyt droga.",
    fit: ["hybrydowy uklad dolnego zrodla", "analiza minimalnej koniecznej ingerencji", "monitoring pracy po uruchomieniu"],
    questions: ["Ile odwiertow realnie jest mozliwych?", "Jaki jest profil zuzycia ciepla?", "Czy PVT ma pelnic role wsparcia czy glownego elementu?"],
  },
  {
    id: "heritage",
    label: "Obiekt zabytkowy",
    meta: "Wyglad, halas i formalnosci sa krytyczne",
    tag: "Konserwator / estetyka / cisza",
    title: "Tu nie wystarczy, ze system dziala. On nie moze przeszkadzac obiektowi",
    pain: "Kazda widoczna jednostka, przewiert, trasa instalacyjna albo halas moze zatrzymac projekt. Potrzebujesz rozwiazania, ktore zaczyna sie od ograniczen, a nie od katalogu.",
    fit: ["wstepna mapa ograniczen formalnych", "minimalizacja widocznych elementow", "wariantowanie tras i miejsca technicznego"],
    questions: ["Jakie sa wymogi konserwatorskie?", "Co moze byc widoczne z zewnatrz?", "Czy obiekt ma juz instalacje PV, kotlownie lub monitoring energii?"],
  },
  {
    id: "independence",
    label: "Chce niezaleznosci",
    meta: "Energia, cieplo, magazyn i taryfy",
    tag: "Strategia energetyczna domu",
    title: "Nie kupujesz ideologii. Kupujesz spokoj, gdy ceny i taryfy sie zmieniaja",
    pain: "Pelna niezaleznosc brzmi dobrze, ale system musi bronic sie liczbami. Dlatego laczymy cieplo, prad, magazyn i sterowanie tylko tam, gdzie wynik jest logiczny.",
    fit: ["PVT, pompa, magazyn energii i taryfy dynamiczne", "scenariusze pracy dzien/noc/sezon", "stopniowa rozbudowa zamiast jednej slepej decyzji"],
    questions: ["Czy celem jest nizszy rachunek, backup czy komfort?", "Jakie zuzycie energii masz dzisiaj?", "Czy magazyn ma wspierac dom, ogrzewanie czy oba obszary?"],
  },
  {
    id: "public-industrial",
    label: "Obiekt publiczny / przemysl",
    meta: "Koszty, ciaglosc, odpowiedzialnosc",
    tag: "B2B i infrastruktura",
    title: "W firmie system ciepla nie jest gadzetem. Jest elementem ryzyka operacyjnego",
    pain: "Hotel, basen, SPA, zaklad produkcyjny, wspolnota albo obiekt publiczny nie kupuje 'pompy'. Kupuje nizsze ryzyko, przewidywalny koszt, monitoring i jasna odpowiedzialnosc za wynik.",
    fit: ["audyt kosztow i infrastruktury", "monitoring pracy instalacji", "wariant techniczno-ekonomiczny dla zarzadu lub wspolnoty"],
    questions: ["Co boli bardziej: rachunki, awarie czy brak danych?", "Kto podejmuje decyzje i jaki ma horyzont zwrotu?", "Czy obiekt wymaga pracy 24/7 lub raportowania?"],
  },
];

const list = document.querySelector("[data-cohort-list]");
const panel = document.querySelector("[data-cohort-panel]");
const select = document.querySelector("[data-case-select]");
const header = document.querySelector("[data-header]");
const nav = document.querySelector("[data-nav]");
const navToggle = document.querySelector("[data-nav-toggle]");
const urgency = document.querySelector("[data-urgency]");
const urgencyOutput = document.querySelector("[data-urgency-output]");
const form = document.querySelector("[data-diagnostic-form]");

function renderCohortButtons() {
  list.innerHTML = cohorts.map((cohort, index) => `
    <button class="cohort-button ${index === 0 ? "is-active" : ""}" type="button" role="option" aria-selected="${index === 0}" data-cohort="${cohort.id}">
      <strong>${cohort.label}</strong>
      <small>${cohort.meta}</small>
    </button>
  `).join("");

  select.innerHTML = cohorts.map((cohort) => `<option value="${cohort.id}">${cohort.label} - ${cohort.meta}</option>`).join("");
}

function renderPanel(cohortId) {
  const cohort = cohorts.find((item) => item.id === cohortId) || cohorts[0];
  panel.innerHTML = `
    <span class="cohort-tag">${cohort.tag}</span>
    <h3>${cohort.title}</h3>
    <p class="pain-line">${cohort.pain}</p>
    <div class="cohort-columns">
      <div>
        <h3>Co sprawdzamy</h3>
        <ul class="fit-list">${cohort.questions.map((item) => `<li>${item}</li>`).join("")}</ul>
      </div>
      <div>
        <h3>Gdzie moze byc sens</h3>
        <ul class="fit-list">${cohort.fit.map((item) => `<li>${item}</li>`).join("")}</ul>
      </div>
    </div>
  `;

  document.querySelectorAll("[data-cohort]").forEach((button) => {
    const active = button.dataset.cohort === cohort.id;
    button.classList.toggle("is-active", active);
    button.setAttribute("aria-selected", String(active));
  });

  select.value = cohort.id;
}

function getSelectedLimits(formData) {
  return formData.getAll("limits");
}

function buildMailto(formData) {
  const cohort = cohorts.find((item) => item.id === formData.get("case"));
  const limits = getSelectedLimits(formData);
  const body = [
    "Dzien dobry,",
    "",
    "Prosze o wstepna rozmowe/audyt w sprawie systemu Auralion/PVT.",
    "",
    `Moja sytuacja: ${cohort ? `${cohort.label} - ${cohort.meta}` : "nie wskazano"}`,
    `Pilnosc: ${formData.get("urgency")} / 5`,
    `Ograniczenia: ${limits.length ? limits.join(", ") : "nie wskazano"}`,
    "",
    "Opis obiektu:",
    formData.get("message") || "Do uzupelnienia podczas rozmowy.",
    "",
    `Kontakt: ${formData.get("contact")}`,
  ].join("\n");

  return `mailto:biuro@elspect.pl?subject=${encodeURIComponent("Auralion - diagnoza systemu PVT")}&body=${encodeURIComponent(body)}`;
}

renderCohortButtons();
renderPanel(cohorts[0].id);

list.addEventListener("click", (event) => {
  const button = event.target.closest("[data-cohort]");
  if (!button) return;
  renderPanel(button.dataset.cohort);
});

select.addEventListener("change", (event) => {
  renderPanel(event.target.value);
});

urgency.addEventListener("input", () => {
  urgencyOutput.textContent = `${urgency.value} / 5`;
});

form.addEventListener("submit", (event) => {
  event.preventDefault();
  const formData = new FormData(form);
  window.location.href = buildMailto(formData);
});

navToggle.addEventListener("click", () => {
  const isOpen = nav.classList.toggle("is-open");
  document.body.classList.toggle("nav-open", isOpen);
  header.classList.toggle("is-open", isOpen);
  navToggle.setAttribute("aria-expanded", String(isOpen));
});

nav.addEventListener("click", (event) => {
  if (event.target.tagName !== "A") return;
  nav.classList.remove("is-open");
  document.body.classList.remove("nav-open");
  header.classList.remove("is-open");
  navToggle.setAttribute("aria-expanded", "false");
});

function updateHeader() {
  header.classList.toggle("is-scrolled", window.scrollY > 24);
}

window.addEventListener("scroll", updateHeader, { passive: true });
updateHeader();
