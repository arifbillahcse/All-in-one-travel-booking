/* =========================================================
   TravelOrio — destination data
   One object per place. Edit text/prices here only; destination.html
   and destination.js render everything from this file.
   Images use placeholder seeds — swap `img` helpers for real photos.
   ========================================================= */
(function () {
  "use strict";

  const img = (slug, key, w, h) => `https://picsum.photos/seed/${slug}-${key}/${w}/${h}`;

  const COMMON_FAQ = [
    {
      q: "Can I customise the itinerary?",
      a: "Absolutely. Tell us your dates, budget and interests in the booking form and we'll adjust the days, hotel and activities.",
    },
    {
      q: "How do I pay?",
      a: "No payment is taken online. After you send a request we confirm availability and share bKash, Nagad or bank transfer details.",
    },
    {
      q: "What is the cancellation policy?",
      a: "Free cancellation up to 7 days before departure. After that, charges depend on hotel and transport bookings already made.",
    },
    {
      q: "Is there a group discount?",
      a: "Groups of 6 or more receive a reduced per-person price. Message us on WhatsApp for a quote.",
    },
  ];

  const places = [
    /* ---------------------------------------------------- Cox's Bazar */
    {
      slug: "coxs-bazar",
      name: "Cox's Bazar",
      region: "Chattogram Division",
      tagline: "Walk the world's longest natural sea beach and watch the sun melt into the Bay of Bengal.",
      price: 4500,
      duration: "2–4 days",
      bestTime: "Nov – Mar",
      distance: "~400 km · 1 hr by air",
      style: "Beach · Relaxed",
      overview: [
        "Cox's Bazar is home to 120 km of unbroken golden sand, the longest natural sea beach in the world. Mornings start with fishermen hauling in their nets, afternoons belong to the surf, and evenings turn the sky orange over the Bay of Bengal.",
        "Beyond the main beach you'll find quiet coves, hill-top pagodas, tribal villages and fresh seafood grilled on the sand. With TravelOrio your hotel, transport and local guide are arranged before you arrive, so you can simply enjoy the coast.",
      ],
      highlights: [
        "Sunrise and sunset on Laboni Beach",
        "Boat trip to Sonadia Island",
        "Scenic drive along Marine Drive",
        "Fresh seafood dinner by the sea",
        "Himchari waterfall and national park",
        "Buddhist temples and local markets",
      ],
      attractions: [
        { name: "Laboni Beach", text: "The main beach and the best place for swimming, parasailing and sunset photos." },
        { name: "Himchari", text: "Cliffs, a small waterfall and a hilltop viewpoint over the sea." },
        { name: "Inani Beach", text: "Coral-stone shoreline with calmer water and fewer crowds." },
        { name: "Sonadia Island", text: "A quiet island reached by boat, known for birds and red crabs." },
      ],
      itinerary: [
        { title: "Arrival and sunset on Laboni Beach", items: ["Airport or bus-stop pickup and hotel check-in", "Lunch at a seaside restaurant", "Free time to swim and relax", "Sunset walk along Laboni Beach, followed by dinner"] },
        { title: "Marine Drive, Inani and Himchari", items: ["Breakfast, then a scenic drive along Marine Drive", "Stop at Himchari for the waterfall and viewpoint", "Lunch and a swim at Inani Beach", "Evening at the local seafood market"] },
        { title: "Sonadia Island boat trip", items: ["Early boat ride to Sonadia Island", "Bird watching, shell hunting and a beach picnic", "Back to the hotel by afternoon", "Optional shopping for pearls, dry fish and handicrafts"] },
        { title: "Sunrise and departure", items: ["Sunrise on the beach with tea", "Breakfast and hotel check-out", "Drop-off at the airport or bus station"] },
      ],
      included: ["Hotel stay (twin-sharing)", "Daily breakfast", "Airport or bus-stop pickup and drop", "Private AC vehicle for sightseeing", "Local English or Bangla-speaking guide", "Boat ticket to Sonadia Island"],
      excluded: ["Flights or long-distance bus tickets", "Lunch and dinner (unless upgraded)", "Personal expenses and shopping", "Water sports and optional activities", "Tips for guides and drivers"],
      seasons: [
        { tone: "best", badge: "Best", range: "Nov – Feb", text: "Cool, dry and sunny. Calm sea, ideal for beach days and boat trips." },
        { tone: "good", badge: "Good", range: "Mar – May", text: "Warmer with fewer crowds and lower prices. Pack sun protection." },
        { tone: "wet", badge: "Rainy", range: "Jun – Oct", text: "Dramatic waves and green hills, but boat trips may be cancelled." },
      ],
      transport: [
        "<strong>By air:</strong> about 1 hour from Dhaka, with several daily flights.",
        "<strong>By bus:</strong> 9–11 hours overnight AC coach from Dhaka.",
        "<strong>By train:</strong> Dhaka to Cox's Bazar by rail, about 8–9 hours.",
      ],
      tips: ["Book hotels early for Eid and winter weekends.", "Swim only in flagged safe zones.", "Carry cash for small shops and tuk-tuks.", "Dress modestly when visiting temples and villages."],
      faq: [
        { q: "Is Cox's Bazar safe for families and solo women travelers?", a: "Yes. The main tourist areas are busy and well patrolled. Our guides stay with you, and we choose hotels with good reviews and secure entrances." },
      ],
      gallery: ["Golden sunset over the Cox's Bazar sea", "Fishing boats on the shore", "Waves rolling onto the beach", "Grilled seafood dinner", "Marine Drive coastal road", "Pagoda on a green hill"],
      related: ["saint-martin", "bandarban", "kuakata"],
    },

    /* ---------------------------------------------------- Sundarbans */
    {
      slug: "sundarbans",
      name: "Sundarbans",
      region: "Khulna Division",
      tagline: "Cruise silent tidal creeks through the largest mangrove forest on Earth.",
      price: 8500,
      duration: "3 days",
      bestTime: "Nov – Feb",
      distance: "~330 km · Khulna / Mongla",
      style: "Wildlife · Cruise",
      overview: [
        "The Sundarbans is a UNESCO World Heritage mangrove forest, a maze of tidal rivers and creeks that shelters spotted deer, crocodiles, monkeys, hundreds of bird species and the Royal Bengal tiger.",
        "You'll sleep aboard a licensed launch, explore by small boat and walk forest trails with an armed forest guard. Tigers are shy, so sightings are rare, but fresh tracks, birdsong and the quiet of the creeks make every hour memorable.",
      ],
      highlights: [
        "Overnight launch cruise through tidal creeks",
        "Spot deer, crocodiles, monkeys and kingfishers",
        "Watchtower walks at Karamjal and Kotka",
        "Sunrise at Hiron Point (Nilkamal)",
        "Visit the fishing village of Dublar Char",
        "Evening tea and stories on the deck",
      ],
      attractions: [
        { name: "Karamjal", text: "A wildlife centre with crocodiles and deer, and the usual first stop of the cruise." },
        { name: "Hiron Point (Nilkamal)", text: "Deep forest where deer graze and tiger tracks are often found on the mud." },
        { name: "Kotka", text: "A quiet beach and watchtower at the edge of the forest and the sea." },
        { name: "Dublar Char", text: "A seasonal island where fishing families dry their catch on the sand." },
      ],
      itinerary: [
        { title: "Board the launch and visit Karamjal", items: ["Meet at Khulna or Mongla and board the launch", "Lunch onboard while cruising into the forest", "Afternoon visit to Karamjal wildlife centre", "Dinner and overnight on the launch"] },
        { title: "Hiron Point and Kotka", items: ["Early morning creek cruise for birds and deer", "Forest walk at Hiron Point with the guard", "Lunch, then Kotka beach and watchtower", "Campfire-style evening on deck"] },
        { title: "Dublar Char and return", items: ["Sunrise cruise to Dublar Char", "Meet the fishing community", "Breakfast onboard and return journey", "Drop-off at Mongla or Khulna by evening"] },
      ],
      included: ["Shared cabin on a licensed launch", "All meals onboard", "Forest permits and entry fees", "Armed forest guard and local guide", "Pickup and drop at Khulna or Mongla", "Drinking water"],
      excluded: ["Travel from Dhaka to Khulna", "Camera and video fees", "Soft drinks and personal purchases", "Travel insurance", "Tips for crew and guides"],
      seasons: [
        { tone: "best", badge: "Best", range: "Nov – Feb", text: "Cool and dry with clear skies and the best chance to see wildlife by the water." },
        { tone: "good", badge: "Good", range: "Mar – May", text: "Hotter, but animals gather at the creeks. Bring a hat and plenty of water." },
        { tone: "wet", badge: "Rainy", range: "Jun – Oct", text: "Rough water, and the forest is often closed to visitors in the wettest months." },
      ],
      transport: [
        "<strong>By air:</strong> Dhaka to Jashore in about 50 minutes, then 2 hours by road to Khulna.",
        "<strong>By train:</strong> overnight train from Dhaka to Khulna, about 9–10 hours.",
        "<strong>By bus:</strong> 6–8 hours from Dhaka to Khulna via the Padma Bridge.",
      ],
      tips: ["A forest permit is required, and we arrange it for you.", "Carry mosquito repellent, a hat and a light rain jacket.", "Never leave the guide's side on forest trails.", "Take your plastic waste back with you."],
      faq: [
        { q: "Will I see a Royal Bengal tiger?", a: "Sightings are rare because tigers avoid people. You are far more likely to see tracks, deer, crocodiles, monkeys and many birds." },
      ],
      gallery: ["Mangrove creek in morning mist", "Our launch moored in the forest", "Spotted deer by the water", "Kingfisher on a branch", "Watchtower above the trees", "Fishing boats at Dublar Char"],
      related: ["sylhet", "coxs-bazar", "kuakata"],
    },

    /* ---------------------------------------------------- Sylhet */
    {
      slug: "sylhet",
      name: "Sylhet",
      region: "Sylhet Division",
      tagline: "Mist-wrapped tea hills, swamp forests and crystal streams in Bangladesh's green north-east.",
      price: 5500,
      duration: "2–3 days",
      bestTime: "Oct – Mar",
      distance: "~240 km · 40 min by air",
      style: "Nature · Tea gardens",
      overview: [
        "Sylhet is Bangladesh at its greenest. Tea estates roll over the hills, rivers run clear from the Meghalaya mountains, and a flooded swamp forest turns into a floating-world of tree trunks and boats.",
        "Days mix easy walks through tea gardens, boat rides, riverside picnics and local food such as seven-layer tea. It is an easy trip from Dhaka and a favourite for families and couples.",
      ],
      highlights: [
        "Walk through Malnicherra tea estate",
        "Boat ride in Ratargul Swamp Forest",
        "Stone-collecting rivers at Jaflong",
        "Clear streams and picnic spots at Sripur",
        "Seven-layer tea tasting",
        "Visit the shrine of Hazrat Shah Jalal",
      ],
      attractions: [
        { name: "Ratargul Swamp Forest", text: "Row through a flooded forest of tree trunks, best when water levels are high." },
        { name: "Jaflong", text: "A river valley on the Meghalaya border with stone collectors and mountain views." },
        { name: "Malnicherra Tea Estate", text: "The oldest commercial tea garden in the region, with winding paths and viewpoints." },
        { name: "Lalakhal", text: "A calm river with green-blue water, perfect for a slow boat ride." },
      ],
      itinerary: [
        { title: "Tea gardens and the city", items: ["Pickup from airport or station and hotel check-in", "Visit Malnicherra tea estate", "Tea tasting and local lunch", "Evening at the shrine of Hazrat Shah Jalal"] },
        { title: "Ratargul and Lalakhal", items: ["Early drive to Ratargul Swamp Forest", "Boat ride through the flooded trees", "Lunch, then a boat ride on Lalakhal", "Return for dinner in Sylhet city"] },
        { title: "Jaflong and Sripur", items: ["Drive to Jaflong river valley", "Walk along the stone beds and the border view", "Picnic at the clear streams of Sripur", "Drop-off at the airport or bus stop"] },
      ],
      included: ["Hotel stay (twin-sharing)", "Daily breakfast", "Private AC vehicle for sightseeing", "Local guide", "Boat tickets at Ratargul and Lalakhal", "Pickup and drop at Sylhet"],
      excluded: ["Flights, train or bus tickets to Sylhet", "Lunch and dinner (unless upgraded)", "Personal expenses and shopping", "Tips for guides and drivers"],
      seasons: [
        { tone: "best", badge: "Best", range: "Oct – Mar", text: "Cool, clear and sunny. Ideal for tea gardens, Jaflong and Sripur." },
        { tone: "good", badge: "Good", range: "Apr – May", text: "Warm and green with thunderstorms in the afternoon." },
        { tone: "wet", badge: "Wet", range: "Jun – Sep", text: "Heavy rain, but this is when Ratargul fills with water and is at its most magical." },
      ],
      transport: [
        "<strong>By air:</strong> about 40 minutes from Dhaka, with daily flights.",
        "<strong>By train:</strong> intercity trains take 6–7 hours from Dhaka.",
        "<strong>By bus:</strong> 5–7 hours on the Dhaka–Sylhet highway.",
      ],
      tips: ["Carry a light raincoat all year round.", "Wear shoes with good grip for tea-garden paths.", "Ratargul is best visited early to avoid crowds.", "Respect border zones and follow your guide's instructions."],
      faq: [
        { q: "When is the best time to visit Ratargul?", a: "June to September, when the water is high enough for boats to move between the trees. In the dry season the forest is mostly dry land." },
      ],
      gallery: ["Tea estate in morning mist", "Boat in Ratargul Swamp Forest", "Clear river at Jaflong", "Seven-layer tea", "Green hills near Sripur", "Quiet stretch of Lalakhal"],
      related: ["bandarban", "sundarbans", "coxs-bazar"],
    },

    /* ---------------------------------------------------- Bandarban */
    {
      slug: "bandarban",
      name: "Bandarban",
      region: "Chattogram Hill Tracts",
      tagline: "Rise above the clouds in Bangladesh's wild hill country.",
      price: 6000,
      duration: "3 days",
      bestTime: "Oct – Mar",
      distance: "~330 km · 8–10 hrs by road",
      style: "Hills · Adventure",
      overview: [
        "Bandarban is Bangladesh's high country, where misty ridges, deep valleys and rushing waterfalls meet the homes of several indigenous communities. Mornings often start above a sea of clouds.",
        "Roads are steep and views are huge. We handle local permits, jeeps and hill-community guides so you can focus on sunrise viewpoints, waterfalls and warm hospitality in the villages.",
      ],
      highlights: [
        "Sea-of-clouds sunrise at Nilgiri",
        "Swim below Nafakhum Waterfall",
        "Visit the Golden Temple (Buddha Dhatu Jadi)",
        "Boat ride on the Sangu River",
        "Meet hill communities and taste local food",
        "Chimbuk Hill viewpoint at golden hour",
      ],
      attractions: [
        { name: "Nilgiri", text: "A hilltop viewpoint at 2,200 ft, famous for cloud-level sunrises." },
        { name: "Nafakhum Waterfall", text: "A wide waterfall on the Sangu River, reached by boat and a short walk." },
        { name: "Golden Temple", text: "One of the largest Buddhist temples in the country, with gilded statues on a hill." },
        { name: "Boga Lake", text: "A high-altitude lake for trekkers who want a longer adventure." },
      ],
      itinerary: [
        { title: "Arrival and the Golden Temple", items: ["Arrive in Bandarban and check in", "Visit the Golden Temple", "Sunset at Meghla viewpoint", "Dinner with local flavours"] },
        { title: "Nilgiri and Chimbuk", items: ["Early jeep ride to Nilgiri for sunrise above the clouds", "Breakfast with a view", "Afternoon at Chimbuk Hill", "Visit a local village"] },
        { title: "Sangu River and Nafakhum", items: ["Boat ride along the Sangu River", "Short walk to Nafakhum Waterfall", "Swim and picnic lunch", "Return journey and departure"] },
      ],
      included: ["Hotel or resort stay", "Daily breakfast", "Local jeep (Chander Gari) for hill roads", "Hill-community guide", "Boat tickets", "Local permits where required"],
      excluded: ["Transport from Dhaka to Bandarban", "Lunch and dinner (unless upgraded)", "Personal expenses and shopping", "Tips for guides and drivers"],
      seasons: [
        { tone: "best", badge: "Best", range: "Oct – Feb", text: "Clear skies, cool air and the best sea-of-clouds mornings." },
        { tone: "good", badge: "Good", range: "Mar – May", text: "Hot days but great waterfalls. Start early and carry water." },
        { tone: "wet", badge: "Rainy", range: "Jun – Sep", text: "Lush and dramatic, but roads can be slippery and some trails close." },
      ],
      transport: [
        "<strong>By bus:</strong> 8–10 hours from Dhaka via Chattogram.",
        "<strong>By air + road:</strong> fly to Chattogram (1 hour), then 3–3.5 hours by road.",
        "<strong>Local travel:</strong> only local jeeps are used on hill roads, and we book them for you.",
      ],
      tips: ["Roads are steep and winding, so bring motion-sickness tablets if needed.", "Wear shoes with grip for waterfalls and trails.", "Ask before photographing people in villages.", "Some areas need permits or local escorts, and we arrange them."],
      faq: [
        { q: "Is a permit needed for Bandarban?", a: "Some areas and treks require permission from the local administration. We handle this for you, and we always confirm current rules before your trip." },
      ],
      gallery: ["Sea of clouds at sunrise", "Nafakhum Waterfall", "Golden Temple on the hill", "Jeep on a hill road", "Village life in the hills", "Sangu River at dusk"],
      related: ["sylhet", "coxs-bazar", "saint-martin"],
    },

    /* ---------------------------------------------------- Saint Martin's */
    {
      slug: "saint-martin",
      name: "Saint Martin's Island",
      region: "Bay of Bengal",
      tagline: "Bangladesh's only coral island, ringed by turquoise water and coconut palms.",
      price: 7000,
      duration: "2–3 days",
      bestTime: "Nov – Feb",
      distance: "~9 km offshore · via Teknaf",
      style: "Island · Beach",
      overview: [
        "Saint Martin's is a tiny coral island at the southern tip of the country. Clear water, coconut palms, quiet lanes and a slow rhythm of life make it feel a world away from the mainland.",
        "The journey itself is part of the fun: a coastal drive to Teknaf, then a ship across the bay. Once there, days are for swimming, walking the shoreline, tasting fresh fish and watching the sunset from the rocks.",
      ],
      highlights: [
        "Ship ride across the Bay of Bengal",
        "Swim in clear, turquoise water",
        "Walk to Chhera Dwip at low tide",
        "Sunset from the western shore",
        "Fresh seafood and coconut water",
        "Stargazing on the beach",
      ],
      attractions: [
        { name: "Chhera Dwip", text: "A small rocky outcrop at the island's tip, reachable on foot at low tide." },
        { name: "Coral shoreline", text: "Rock and coral pools along the beach, with calm swimming spots." },
        { name: "Sunset Point", text: "The west shore, where the sky turns orange over the water." },
        { name: "Island village", text: "Narrow lanes, dried fish racks and friendly local shops." },
      ],
      itinerary: [
        { title: "Teknaf and the ship to the island", items: ["Early pickup and coastal drive to Teknaf", "Board the ship to Saint Martin's", "Check in and lunch", "Evening walk and sunset"] },
        { title: "Chhera Dwip and the shoreline", items: ["Breakfast, then a walk to Chhera Dwip at low tide", "Swim and relax on the beach", "Local seafood lunch", "Stargazing after dinner"] },
        { title: "Return to the mainland", items: ["Sunrise walk", "Breakfast and check-out", "Ship back to Teknaf", "Drive to Cox's Bazar"] },
      ],
      included: ["Resort or cottage stay", "Daily breakfast", "Ship ticket (round trip)", "Transport between Cox's Bazar and Teknaf", "Local guide", "Welcome coconut"],
      excluded: ["Travel to Cox's Bazar", "Lunch and dinner (unless upgraded)", "Personal expenses", "Tips for guides and drivers"],
      seasons: [
        { tone: "best", badge: "Best", range: "Nov – Feb", text: "Calm sea, clear water and comfortable weather for the ship ride." },
        { tone: "good", badge: "Good", range: "Mar", text: "Hotter, but still a regular ship service and good swimming." },
        { tone: "wet", badge: "Closed", range: "Apr – Oct", text: "Rough seas, and ship services are usually suspended in these months." },
      ],
      transport: [
        "<strong>To Cox's Bazar:</strong> fly or take a coach from Dhaka first.",
        "<strong>To Teknaf:</strong> about 2.5–3 hours by road from Cox's Bazar along Marine Drive.",
        "<strong>To the island:</strong> ship from Teknaf, about 2–3 hours, in season.",
      ],
      tips: ["Respect the coral and never collect shells, corals or live creatures.", "Carry cash, as ATMs are not available on the island.", "Bring sun protection and a power bank.", "Rules on overnight stays and ship schedules can change, so we confirm them before booking."],
      faq: [
        { q: "Can we stay overnight on the island?", a: "Overnight rules and ship schedules can change from season to season. We confirm the latest regulations with you before you book, so you are never caught out." },
      ],
      gallery: ["Turquoise water at the shore", "Ship crossing the bay", "Coconut palms on the beach", "Chhera Dwip at low tide", "Sunset over the island", "Fresh fish lunch"],
      related: ["coxs-bazar", "kuakata", "bandarban"],
    },

    /* ---------------------------------------------------- Kuakata */
    {
      slug: "kuakata",
      name: "Kuakata",
      region: "Barishal Division",
      tagline: "Watch the sun rise and set over the same sea.",
      price: 4000,
      duration: "2 days",
      bestTime: "Nov – Mar",
      distance: "~320 km · Barishal Division",
      style: "Beach · Culture",
      overview: [
        "Kuakata is one of the few places in South Asia where you can watch both sunrise and sunset over the sea from the same beach. The wide, gently sloping shore is quieter than other coasts and perfect for slow family trips.",
        "Beyond the sea, you'll find mangrove islands, the vibrant Rakhine Buddhist community, riverside villages and plenty of fresh river fish.",
      ],
      highlights: [
        "Sunrise and sunset on the same beach",
        "Boat trip to Fatrar Char mangrove island",
        "Walk through Gangamati Reserved Forest",
        "Visit the Rakhine Buddhist temple",
        "Local handicraft shopping",
        "Fresh hilsa and river fish",
      ],
      attractions: [
        { name: "Kuakata Sea Beach", text: "A wide, sandy beach with calm waves and uninterrupted horizons." },
        { name: "Fatrar Char", text: "A mangrove-fringed island reached by boat, great for birds and quiet." },
        { name: "Gangamati Forest", text: "A reserved coastal forest with walking trails and shade." },
        { name: "Rakhine Temple", text: "A peaceful Buddhist temple with a large Buddha statue and local craft shops." },
      ],
      itinerary: [
        { title: "Arrival and sunset by the sea", items: ["Arrive in Kuakata and check in", "Lunch with fresh river fish", "Free time on the beach", "Sunset walk and dinner"] },
        { title: "Sunrise, Fatrar Char and departure", items: ["Sunrise on the beach with tea", "Breakfast, then a boat trip to Fatrar Char", "Visit the Rakhine temple and craft market", "Check-out and return"] },
      ],
      included: ["Hotel stay (twin-sharing)", "Daily breakfast", "Local transport and guide", "Boat ticket to Fatrar Char", "Pickup and drop at Kuakata"],
      excluded: ["Transport from Dhaka to Kuakata", "Lunch and dinner (unless upgraded)", "Personal expenses and shopping", "Tips for guides and drivers"],
      seasons: [
        { tone: "best", badge: "Best", range: "Nov – Feb", text: "Cool, clear skies and the best sunrise and sunset views." },
        { tone: "good", badge: "Good", range: "Mar – May", text: "Warm and quiet, with lower hotel prices." },
        { tone: "wet", badge: "Rainy", range: "Jun – Oct", text: "Stormy skies and rough waves, and some boat trips may stop." },
      ],
      transport: [
        "<strong>By bus:</strong> about 6–8 hours from Dhaka via the Padma Bridge.",
        "<strong>By launch:</strong> overnight launch to Barishal, then about 2.5–3 hours by road.",
        "<strong>By air:</strong> fly to Barishal (about 35 minutes), then 2.5–3 hours by road.",
      ],
      tips: ["Arrive before sunset on the first day so you catch both views.", "Sea currents can be strong, so swim only where locals do.", "Carry cash and mosquito repellent.", "Dress modestly at the temple."],
      faq: [
        { q: "Is Kuakata good for a short weekend trip?", a: "Yes. A two-day plan is enough to see both sunrise and sunset, the mangrove island and the temple, though the journey from Dhaka is long." },
      ],
      gallery: ["Sunrise over the Kuakata sea", "Wide, quiet beach", "Fishing boats at dawn", "Mangrove at Fatrar Char", "Buddha statue at the temple", "Fresh river fish lunch"],
      related: ["coxs-bazar", "saint-martin", "sundarbans"],
    },
  ];


  /* ---------------------------------------------------------
     Sample reviews — placeholders. Replace with real ones
     (keep the fields) before launch.
     slug = destination slug, date = YYYY-MM
     --------------------------------------------------------- */
  const reviews = [
    { name: "Rahim Uddin", city: "Dhaka", slug: "sundarbans", rating: 5, date: "2026-02", type: "Friends", featured: true,
      title: "Flawless from start to finish",
      text: "The Sundarbans trip was flawless. Our guide spotted tiger tracks on the very first morning, and the launch was clean and comfortable. Every detail had been handled before we even arrived. Highly recommended." },
    { name: "Nusrat Jahan", city: "Chattogram", slug: "coxs-bazar", rating: 5, date: "2026-01", type: "Family",
      title: "Perfect for a family of five",
      text: "Everything was organised perfectly for our family of five. The hotel was right on the beach, the driver was patient with the kids, and the Sonadia boat trip was the highlight for all of us." },
    { name: "Tanvir Ahmed", city: "Sylhet", slug: "bandarban", rating: 5, date: "2025-12", type: "Friends",
      title: "Magical, even in the clouds",
      text: "Bandarban in winter was magical. Fair prices, kind staff and quick replies on WhatsApp throughout. The Nilgiri sunrise above the clouds is something I will never forget." },
    { name: "Sadia Rahman", city: "Dhaka", slug: "saint-martin", rating: 5, date: "2026-02", type: "Couple",
      title: "Our quietest, happiest weekend",
      text: "We wanted to switch off and Saint Martin's was perfect. TravelOrio explained the ship timings and island rules clearly beforehand, so nothing surprised us. Sunset on the rocks was unreal." },
    { name: "Imran Hossain", city: "Khulna", slug: "sylhet", rating: 5, date: "2025-11", type: "Couple",
      title: "Tea gardens and swamp forest",
      text: "A beautifully paced three days. Ratargul by boat was surreal, and our guide knew exactly when to arrive to avoid the crowds. The seven-layer tea was a lovely bonus." },
    { name: "Farzana Akter", city: "Rajshahi", slug: "kuakata", rating: 4, date: "2026-01", type: "Family",
      title: "Lovely beach, long journey",
      text: "Seeing sunrise and sunset from the same beach was wonderful and the Rakhine temple was peaceful. The road from Dhaka is long, but the team kept us updated and the hotel was spotless." },
    { name: "Mahfuz Alam", city: "Dhaka", slug: "coxs-bazar", rating: 5, date: "2025-12", type: "Solo",
      title: "Great for solo travelers",
      text: "I travelled alone and felt looked after the entire time. The guide suggested quieter spots like Inani, and the single-room price was fair. I would book again without thinking." },
    { name: "Tahmina Sultana", city: "Dhaka", slug: "bandarban", rating: 5, date: "2026-03", type: "Family",
      title: "Careful with permits and safety",
      text: "I was nervous about the hills with my parents, but the team handled the permits, the jeeps and a gentle itinerary. My father still talks about the Golden Temple." },
    { name: "Rafiqul Islam", city: "Bogura", slug: "sundarbans", rating: 5, date: "2025-11", type: "Family",
      title: "Educational and peaceful",
      text: "The kids learnt so much about the mangroves and the animals. Food on the launch was fresh and tasty, and the crew was respectful and careful throughout." },
    { name: "Nabila Karim", city: "Dhaka", slug: "saint-martin", rating: 5, date: "2026-01", type: "Friends",
      title: "Clear water, clear communication",
      text: "Booking was easy over WhatsApp, and every question got a quick, honest answer. The island was exactly as promised: turquoise water, coconut palms and no rush." },
    { name: "Shamim Reza", city: "Narayanganj", slug: "sylhet", rating: 5, date: "2026-03", type: "Friends",
      title: "Worth every taka",
      text: "Jaflong and Sripur in one day sounded tiring, but the schedule was relaxed with proper breaks. Prices were exactly what was quoted, with no surprise charges at the end." },
    { name: "Priya Das", city: "Barishal", slug: "kuakata", rating: 5, date: "2025-12", type: "Couple",
      title: "A calm, beautiful short break",
      text: "Two days was just right. We caught a golden sunset on the first evening and a misty sunrise the next morning. Fatrar Char by boat was a pleasant surprise." },
  ];

  // Fill in derived fields
  places.forEach((p) => {
    p.heroImage = img(p.slug, "hero", 1920, 1080);
    p.attractions.forEach((a, i) => { a.img = img(p.slug, "a" + (i + 1), 640, 420); });
    p.faq = p.faq.concat(COMMON_FAQ);
    p.cardImage = `https://picsum.photos/seed/${p.slug.replace("-", "")}/800/600`;
  });

  window.TRAVELORIO_DESTINATIONS = places;
  window.TRAVELORIO_REVIEWS = reviews;
  window.TRAVELORIO_IMG = img;
})();
