let showform = null;
let comparePopup = null;
let renderAlert = null;
let renderMedia = null;
const IDEAS_DATA = {
    year_count: 14,
    students_count: 2571,
    courses_count: 74,
    teachers_count: 28,
    facebook_link: "https://www.facebook.com/ideas.edu.vn/",
    youtube_link: "https://www.youtube.com/c/Vi%E1%BB%87nIDEAS",
    zalo_link: "https://zalo.me/3857867121882640296",
    linkedin_link: "https://www.linkedin.com/company/ideasinstitute/",
    tiktok_link: "https://www.tiktok.com/@ideas_institute",
    programmes: {
        IDEAS01: {
            benefits: [
                "High-quality 3-year Bachelor of Business Administration (180 ECTS) program from Switzerland.",
                "100% flexible online learning on a modern platform, suitable for working professionals and high school graduates.",
                "IDEAS support: IDEAS-LMS system, AI learning assistant, weekend auxiliary seminars.",
                "Prestigious graduation ceremony in Geneva, Switzerland with European travel opportunities."
            ],
            program_name_degree: "Bachelor of Business Administration",
            program_benefits_degree: [
                "European standard Bachelor's degree awarded by the prestigious Swiss UMEF University in Switzerland.",
                "Comprehensive business administration knowledge base and international integration capability.",
                "Direct pathway to international standard Master's programs (MBA, MSc AI)."
            ],
            link_iframe: "https://www.youtube.com/embed/ZrLeuFGGXQI?si=0tiJvbnRDzwEyo3B",
            listImgs: [
                "https://ideas.edu.vn/wp-content/uploads/2026/06/ltnumef10202501.webp",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSC_9177.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6555.jpg"
            ],
            level: "BBA",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/09/Logo-Swiss-UMEF.webp",
            name: "Full BBA",
            highlight: [
                "100% Online",
                "BBA",
                "36 Months (3 Years)",
                "180 ECTS"
            ],
            school: "Swiss UMEF",
            country: "Switzerland",
            subjects: "<b>180</b> ECTS - <b>34</b> courses and Graduation Thesis",
            duration: "36 months",
            tagline: "Swiss UMEF: First private university in Geneva to achieve the highest Swiss Federal Accreditation (SAC) - officially recognized by the Swiss Higher Education Council",
            link: "/en/fullbba",
            experience: [
                "High school graduate or equivalent",
                "Passed the Admissions Board screening requirements",
                "English proficiency test or entry assessment interview"
            ],
            fee_course: [
                {
                    name: "Standard",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon6.png",
                    price: "8,050 CHF (Full 3-year package)",
                    benefits: [
                        "Basic tuition fee with 80% scholarship applied from support fund.",
                        "Original Swiss UMEF LMS study account.",
                        "Admissions procedures and student profile support in Vietnam."
                    ]
                }
            ],
            description: "The 3-year Bachelor of Business Administration (Full BBA) program from Swiss UMEF provides students with a comprehensive foundation of international business knowledge, expanding career opportunities and laying a solid foundation for higher studies.",
            degree: {
                front: "https://ideas.edu.vn/wp-content/uploads/2026/06/bba-degree.webp",
                back: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0002.webp"
            },
            require: [
                "High school diploma and academic transcripts",
                "Curriculum Vitae (CV)",
                "Motivation Letter",
                "Passport photo (4x6)",
                "Valid passport copy",
                "Swiss UMEF Application Form"
            ],
            this_subjects: [
                { name: "Rhetoric and Composition", description: "Academic writing skills and professional text presentation", credit: 5 },
                { name: "Financial Mathematics", description: "Applied financial mathematics in business modeling", credit: 5 },
                { name: "Introduction to Marketing", description: "Basic concepts and principles of marketing", credit: 5 },
                { name: "Economics", description: "Foundational microeconomics and macroeconomics", credit: 5 },
                { name: "Digital Marketing", description: "Applying digital technology to marketing and advertising", credit: 5 },
                { name: "Critical Thinking", description: "Critical thinking and logical problem solving", credit: 5 },
                { name: "Statistics Applied to Business", description: "Applied statistics for business data analysis", credit: 5 },
                { name: "Human Resource Management", description: "Managing human resources in organizations", credit: 5 },
                { name: "Corporate Finance", description: "Basic corporate finance and cash flow", credit: 5 },
                { name: "International Business", description: "International business and trade barriers", credit: 5 },
                { name: "Business Law", description: "Business law and legal commitments", credit: 5 },
                { name: "Communication Strategy", description: "Internal communication strategy and public relations", credit: 5 },
                { name: "Managerial Accounting", description: "Managerial accounting for internal decision making", credit: 5 },
                { name: "International Management", description: "Multinational management and global value chain operation", credit: 5 },
                { name: "Risk Management", description: "Financial and operational risk management", credit: 5 },
                { name: "Entrepreneurship and Small Business Management", description: "Practical entrepreneurship and small business management", credit: 5 },
                { name: "Business Negotiation", description: "Art of negotiation and persuasion in business", credit: 5 },
                { name: "Purchasing and Supply Chain", description: "Global purchasing and supply chain management", credit: 5 },
                { name: "Leadership and Team Building", description: "Leadership skills and effective team building", credit: 5 },
                { name: "Marketing of Luxury Products", description: "Marketing of luxury products and niche positioning", credit: 5 },
                { name: "Business Policy and Strategy", description: "Corporate policy and strategic management", credit: 5 },
                { name: "Geopolitics and Geo-Economics", description: "Geopolitics and geo-economics global impact", credit: 5 },
                { name: "Sustainable Development", description: "Sustainable development and ESG social responsibility", credit: 5 },
                { name: "Thesis Methodology", description: "Scientific research methodology for graduation thesis", credit: 5 },
                { name: "Introduction to Management", description: "Introduction to modern management science", credit: 5 },
                { name: "Global Marketing", description: "Global marketing and international market penetration", credit: 5 },
                { name: "Organizational Behaviour", description: "Organizational behavior and corporate culture", credit: 5 },
                { name: "Project Management and Decision Making", description: "Project management and data-driven decision making", credit: 5 },
                { name: "Introduction to Financial Accounting", description: "Basic financial accounting and financial statements", credit: 5 },
                { name: "Total Quality Management", description: "Total quality management in enterprises", credit: 5 },
                { name: "Innovation Management", description: "Innovation management driving business growth", credit: 5 },
                { name: "Change Management", description: "Change management and corporate restructuring", credit: 5 },
                { name: "Management Information Systems", description: "Management information systems for process optimization", credit: 5 },
                { name: "AI in Business", description: "AI applications to enhance business productivity", credit: 5 },
                { name: "Dissertation", description: "BBA bachelor graduation thesis on practical applied research", credit: 15 }
            ]
        },
        IDEAS02: {
            benefits: [
                "Interactive online sessions with foreign professors are mandatory. Enhances capability, communication confidence, discussion with professors, and practical business problem-solving.",
                "I-AI chatbot technology supports relevant content in the online MBA program managed by IDEAS. Program assistants help remind homework deadlines, system issues, and connections.",
                "IDEAS supports: IDEAS-LMS system and auxiliary seminars on Sundays, providing homework guidance and preliminary final exam evaluations.",
                "Graduation ceremony and academic study trip at the main campus - Geneva, Switzerland.",
            ],
            program_name_degree: "Master of Business Administration",
            program_benefits_degree: [
                "A prestigious Master's degree awarded by a long-standing and reputable university.",
                "As a Swiss UMEF alumnus, the new Master's degree marks an important milestone on your path.",
                "Officially become an expert - developing the leadership skills necessary to drive positive change in your field.",
            ],
            link_iframe: "https://www.youtube.com/embed/ZrLeuFGGXQI?si=o8eCXmmewBzKvCgT",
            listImgs: [
                "https://ideas.edu.vn/wp-content/uploads/2026/06/ltnumef10202501.webp",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSC_9177.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6555.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6740.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6777.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4768.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4712.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4367.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4528.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4783.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4447.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4356.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4861.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4840.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4799.jpg",
            ],
            level: "MBA",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/09/online-mba-1.png.webp",
            name: "Online MBA",
            school: "Swiss UMEF",
            subjects: "<b>90</b> ECTS credits - <b>12</b> courses and thesis",
            duration: "18 months",
            country: "Switzerland",
            experience: [
                "Bachelor's degree graduate",
                "Minimum 3 years of professional work experience",
                "Good conversational English or IELTS 6.0 equivalent",
            ],
            test: {
                high: [
                    "Interactive live classes, allowing discussions with professors and peers",
                    "In-depth management knowledge combined with practical case studies",
                    "DBA pathway with various scholarships from the university and IDEAS",
                    "Flexible tuition payment plans or installments",
                ],
                stand: [
                    "Flexible study and research schedule, supported by Vietnamese advisors during the course",
                    "In-depth management knowledge combined with practical case studies",
                    "DBA pathway with various scholarships from the university and IDEAS",
                    "Flexible tuition payment plans or installments",
                ],
            },
            highlight: [
                "100% Online",
                "Master's Degree",
                "20:00 - 23:00 (Vietnam)",
                "2 sessions/course (Sunday)",
            ],
            tagline: "Swiss UMEF: First private university in Geneva to achieve the highest Swiss Federal Accreditation (SAC) - officially recognized by the Swiss Higher Education Council",
            link: "/en/mba",
            description: "The Online MBA program is tailored for busy professionals. The degree is accredited by the Swiss Federal Council, guaranteeing international value, providing practical knowledge, and matching global business trends.",
            demographic: {
                jobs: [
                    { jobname: "Business Administration", percent: 30 },
                    { jobname: "Banking & Finance", percent: 22 },
                    { jobname: "Information Technology", percent: 20 },
                    { jobname: "Start-ups", percent: 16 },
                    { jobname: "Others", percent: 12 },
                ],
                ages: [
                    { jobname: "18 - 24", percent: 7 },
                    { jobname: "25 - 30", percent: 27 },
                    { jobname: "31 - 40", percent: 55 },
                    { jobname: "41 - 50", percent: 10 },
                    { jobname: "51+", percent: 1 },
                ],
            },
            degree: {
                back: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0002.webp",
                front: "https://ideas.edu.vn/wp-content/uploads/2025/11/z7191978013846_36a81ec39301d05fedaf6d4cd0293f9c-1.webp",
                transcript: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0003.webp",
            },
            fee_plane: "3,000",
            fee_course: [
                {
                    name: "High Quality",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon5.png",
                    price: "9,900 CHF",
                    benefits: [
                        "Installment support for 12 - 24 months via Sacombank.",
                        "Includes Standard program and eAcademy learning system.",
                        "Technology application - AI Platform for learning developed by IDEAS.",
                        "Classes with foreign professors on weekday evenings (optional).",
                        "Schedule: each course lasts 4 weeks, with 2 evening classes per week with foreign professors (expected Tue/Thu evenings, 2-3 hours each, 20:00 - 22:00).",
                        "Preliminary final exam evaluation: Evaluated and feedback given by the IDEAS academic council to ensure final assignments are on track, reducing fail rates."
                    ],
                },
            ],
            accreditation: [
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef1.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef3.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef4.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef5.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef2.png", link: "#" },
            ],
            require: [
                "Bachelor's degree and transcripts",
                "Curriculum Vitae (CV)",
                "Motivation Letter",
                "Passport photo (4×6)",
                "Valid passport copy",
                "English level equivalent to IELTS 6.0 or pass the entrance interview with UMEF Vietnam representative",
                "Admission Form <a href='https://ideas.edu.vn/wp-content/uploads/2025/03/ADMISSION-FORM_ENG_MBA-_UMEF.docx' target='_blank' class='text_download'>(Download Here)</a>",
            ],
            faq: [
                {
                    q: "What are the thesis requirements for the MBA program?",
                    a: "For the Swiss UMEF MBA program, students are required to complete a graduation thesis. The thesis is an in-depth study of a practical management issue, helping students apply their learned knowledge to solve a specific problem in their business or industry. <br/><br/> IDEAS will provide group or 1-1 guidance to help students draft outlines, collect data, analyze and present results. The thesis must comply with academic standards regarding content, structure, and research methodology as defined by Swiss UMEF. The thesis should be at least 20,000 words, formatted according to APA standards. Upon completion, the thesis will be evaluated by the academic committee.",
                },
                {
                    q: "Where is the graduation ceremony organized?",
                    a: "Upon completing the Online MBA, students can participate in the graduation ceremony organized by Swiss UMEF. Typically, the graduation ceremony takes place in Geneva, Switzerland, where the main campus is located. However, Swiss UMEF can also organize graduation ceremonies in Vietnam or other locations depending on student enrollment and conditions. Students can choose to attend the ceremony in Geneva or in Vietnam organized in coordination with IDEAS.",
                },
                {
                    q: "What is the value of the Swiss UMEF MBA degree?",
                    a: "The MBA degree awarded by Swiss UMEF (Switzerland) is internationally recognized. Swiss UMEF is the first private university in Geneva to hold Swiss Federal Accreditation, recognized across many countries. It is suitable for professionals looking to enhance management knowledge and develop their careers in international environments.",
                },
            ],
            this_subjects: [
                { name: "MBA 400. Marketing Management", description: "Exploring advanced topics, strategic marketing planning, external and internal environment analysis, consumer behavior, market structure, and new product development.", link: "https://youtu.be/KQ6NgVvctXc?feature=shared", credit: 6 },
                { name: "MBA 401. Human Capital and Talent Management", description: "Managerial orientation of HR activities (recruitment, selection, training, rewards, and performance appraisal) and contemporary issues in talent management.", link: "https://youtu.be/16dkyS-LVes?feature=shared", credit: 6 },
                { name: "MBA 402. Entrepreneurship and Innovation", description: "Principles and practices of entrepreneurship, identifying opportunities, building business models, and strategic launch of new ventures.", link: "", credit: 6 },
                { name: "MBA 403. Corporate Finance", description: "Investment appraisals, compound interest, net present value, internal rate of return, capital budgeting, portfolio theory, and CAPM.", link: "https://youtu.be/wRYf2ZcaeJY?feature=shared", credit: 6 },
                { name: "MBA 404. Accounting for Managers", description: "Understanding financial statements, cost accounting, manager-focused accounting tools, and analytical tools for strategic decisions.", link: "https://youtu.be/daYqOgjHw94?feature=shared", credit: 6 },
                { name: "MBA 405. Digital Transformation", description: "Process of digitalization, strategic response to technology, changes in business models, and assessing new tech opportunities.", link: "", credit: 6 },
                { name: "MBA 406. Strategy Management", description: "Competitive analysis, strategic formulation, positioning, defining firm boundaries, and maximizing long-term profit in uncertain markets.", link: "", credit: 6 },
                { name: "MBA 407. Project Management", description: "Fundamentals of project management, portfolio selection, risk handling, cost and schedule controls, and project termination.", link: "", credit: 6 },
                { name: "MBA 408. Organizational Behaviour", description: "Human behavior in organizations, corporate culture, team dynamics, motivation, power and politics, and organization structure.", link: "", credit: 6 },
                { name: "MBA 409. Leadership Development", description: "Cross-cultural leadership, diversity, organizational interactions, strategic vision, and resolving conflicts in multicultural settings.", link: "https://youtu.be/KK9lDNHYGo4", credit: 6 },
                { name: "MBA 500. Thesis Methodology", description: "Research methodology, thesis planning, writing structure, citations, literature review, and defense preparation.", link: "", credit: 6 },
                { name: "MBA 501. Business Negotiation/AI in Business Decision Making", description: "Standard: AI in Business Decision Making <br/> High Quality: Business Negotiation", link: "", credit: 6 },
                { name: "MBA 505. MBA Thesis", description: "Complete an MBA thesis based on practical research and application of the knowledge learned.", link: "", credit: 18 },
            ],
        },
        IDEAS03: {
            benefits: [
                "Interactive online sessions with foreign professors are mandatory. Enhances capability, communication confidence, discussion with professors, and practical business problem-solving.",
                "I-AI chatbot technology supports relevant content in the online MBA program managed by IDEAS. Program assistants help remind homework deadlines, system issues, and connections.",
                "IDEAS supports: IDEAS-LMS system and auxiliary seminars on Sundays, providing homework guidance and preliminary final exam evaluations.",
                "Graduation ceremony and academic study trip at the main campus - Geneva, Switzerland.",
            ],
            program_name_degree: "Executive Master of Business Administration",
            program_benefits_degree: [
                "A prestigious Master's degree awarded by a long-standing and reputable university.",
                "As a Swiss UMEF alumnus, the new Master's degree marks an important milestone on your path.",
                "Officially become an expert - developing the leadership skills necessary to drive positive change in your field.",
            ],
            link_iframe: "https://www.youtube.com/embed/ZrLeuFGGXQI?si=0tiJvbnRDzwEyo3B",
            listImgs: [
                "https://ideas.edu.vn/wp-content/uploads/2026/06/ltnumef10202501.webp",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSC_9177.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6555.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6740.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6777.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4768.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4712.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4367.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4528.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4783.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4447.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4356.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4861.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4840.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4799.jpg",
            ],
            level: "MBA",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/09/emba.png.webp",
            name: "Executive MBA",
            school: "Swiss UMEF",
            subjects: "<b>60</b> ECTS credits - <b>10</b> courses (no thesis)",
            duration: "14-16 months",
            country: "Switzerland",
            experience: [
                "Bachelor's degree graduate",
                "Minimum 2 years of professional work experience",
                "Good conversational English or IELTS 6.0 equivalent",
            ],
            test: {
                high: [
                    "Interactive live classes, allowing discussions with professors and peers",
                    "Practical-oriented knowledge to solve executive-level management problems",
                    "Streamlined 10-course curriculum - no thesis requirement",
                    "Flexible tuition payment plans or installments",
                ],
                stand: [
                    "Flexible study and research schedule, supported by Vietnamese advisors during the course",
                    "Practical-oriented knowledge to solve executive-level management problems",
                    "Streamlined 10-course curriculum - no thesis requirement",
                    "Flexible tuition payment plans or installments",
                ],
            },
            highlight: [
                "100% Online",
                "Master's Degree",
                "20:00 - 23:00 (Vietnam)",
                "2 sessions/course (Sunday)",
            ],
            tagline: "Swiss UMEF: First private university in Geneva to achieve the highest Swiss Federal Accreditation (SAC) - officially recognized by the Swiss Higher Education Council",
            link: "/en/emba",
            description: "The Online EMBA program is designed for busy professionals. The degree is accredited by the Swiss Federal Council, guaranteeing international value, providing practical knowledge, and matching global business trends.",
            demographic: {
                jobs: [
                    { jobname: "Business Administration", percent: 30 },
                    { jobname: "Banking & Finance", percent: 22 },
                    { jobname: "Information Technology", percent: 20 },
                    { jobname: "Start-ups", percent: 16 },
                    { jobname: "Others", percent: 12 },
                ],
                ages: [
                    { jobname: "18 - 24", percent: 7 },
                    { jobname: "25 - 30", percent: 27 },
                    { jobname: "31 - 40", percent: 55 },
                    { jobname: "41 - 50", percent: 10 },
                    { jobname: "51+", percent: 1 },
                ],
            },
            degree: {
                back: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0002.webp",
                front: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0001.webp",
                transcript: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0003.webp",
            },
            fee_plane: "3,000",
            fee_course: [
                {
                    name: "High Quality",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon5.png",
                    price: "8,900 CHF",
                    benefits: [
                        "Installment support for 12 - 24 months via Sacombank.",
                        "Includes Standard program and eAcademy learning system.",
                        "Technology application - AI Platform for learning developed by IDEAS.",
                        "Classes with foreign professors on weekday evenings (optional).",
                        "Schedule: each course lasts 4 weeks, with 2 evening classes per week with foreign professors (expected Tue/Thu evenings, 2-3 hours each, 20:00 - 22:00).",
                        "Preliminary final exam evaluation: Evaluated and feedback given by the IDEAS academic committee to ensure assignments are on track, reducing fail rates."
                    ],
                },
            ],
            accreditation: [
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef1.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef3.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef4.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef5.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef2.png", link: "#" },
            ],
            require: [
                "Bachelor's degree and transcripts",
                "Curriculum Vitae (CV)",
                "Motivation Letter",
                "Passport photo (4×6)",
                "Valid passport copy",
                "English level equivalent to IELTS 6.0 or pass the entrance interview with UMEF Vietnam representative",
                "Admission Form <a href='https://ideas.edu.vn/wp-content/uploads/2025/03/ADMISSION-FORM_ENG_EMBA-_UMEF.docx' target='_blank' class='text_download'>(Download Here)</a>",
            ],
            faq: [
                {
                    q: "Does the EMBA program require a thesis defense?",
                    a: "No. The Swiss UMEF EMBA program comprises 10 courses and does not require a thesis defense. Students who complete all designated courses will be eligible for the EMBA degree awarded by Swiss UMEF. This helps students optimize their study time and focus on enhancing practical management skills.",
                },
                {
                    q: "Where is the graduation ceremony organized?",
                    a: "Upon completing the EMBA program, students can participate in the graduation ceremony organized by Swiss UMEF. Typically, the graduation ceremony takes place in Geneva, Switzerland, where the main campus is located. However, Swiss UMEF can also organize graduation ceremonies in Vietnam or other locations depending on student enrollment and conditions. Students can choose to attend the ceremony in Geneva or in Vietnam organized in coordination with IDEAS.",
                },
                {
                    q: "Can I pursue a Doctorate after completing the EMBA program?",
                    a: "Yes, after completing the Swiss UMEF EMBA program, students can continue to study for a Doctor of Business Administration (DBA) at suitable universities or institutions. Acceptances into doctoral programs depend on the receiving institution's admission rules. Some schools might require students to take bridging courses. Students can contact IDEAS for specific advice on academic pathways and requirements.",
                },
            ],
            this_subjects: [
                { name: "EMBA 400. Marketing Management", description: "Exploring advanced topics, strategic marketing planning, external and internal environment analysis, consumer behavior, market structure, and new product development.", link: "", credit: 6 },
                { name: "EMBA 401. Human Capital and Talent Management", description: "Managerial orientation of HR activities (recruitment, selection, training, rewards, and performance appraisal) and contemporary issues in talent management.", link: "https://youtu.be/CebD5PCML6w?si=qbXiR8r9eztjvUvM", credit: 6 },
                { name: "EMBA 402. Entrepreneurship and Innovation", description: "Principles and practices of entrepreneurship, identifying opportunities, building business models, and strategic launch of new ventures.", link: "https://youtu.be/t1g7aCRoC-I?si=5luqjqunPivUr5wL", credit: 6 },
                { name: "EMBA 403. Corporate Finance", description: "Investment appraisals, compound interest, net present value, internal rate of return, capital budgeting, portfolio theory, and CAPM.", link: "https://youtu.be/zk1-2CERWHs?si=tT-pLAUoA5iI_b3h", credit: 6 },
                { name: "EMBA 404. Accounting for Managers", description: "Understanding financial statements, cost accounting, manager-focused accounting tools, and analytical tools for strategic decisions.", link: "", credit: 6 },
                { name: "EMBA 405. Digital Transformation", description: "Process of digitalization, strategic response to technology, changes in business models, and assessing new tech opportunities.", link: "", credit: 6 },
                { name: "EMBA 406. Strategy Management", description: "Competitive analysis, strategic formulation, positioning, defining firm boundaries, and maximizing long-term profit in uncertain markets.", link: "https://youtu.be/wVY3uLMG-Fk?si=rTdwcjAk6-tEgbqS", credit: 6 },
                { name: "EMBA 407. Project Management", description: "Fundamentals of project management, portfolio selection, risk handling, cost and schedule controls, and project termination.", link: "https://youtu.be/MMYVUtpiAPk?si=R1XbXLyILON4-yK6", credit: 6 },
                { name: "EMBA 408. Organizational Behaviour", description: "Human behavior in organizations, corporate culture, team dynamics, motivation, power and politics, and organization structure.", link: "", credit: 6 },
                { name: "EMBA 409. Leadership Development", description: "Cross-cultural leadership, diversity, organizational interactions, strategic vision, and resolving conflicts in multicultural settings.", link: "", credit: 6 },
            ],
        },
        IDEAS04: {
            benefits: [
                "The program provides foundational AI knowledge and practical applications, helping business leaders deploy AI to optimize operations, decision-making, and sustainable growth.",
                "Deep understanding of modern AI technologies. Applying AI in management, data analysis, and decision-making.",
                "Developing leadership skills in high-tech environments.",
                "IDEAS supports: IDEAS-LMS system and auxiliary seminars on Sundays, providing homework guidance and preliminary final exam evaluations.",
                "Graduation ceremony and academic study trip at the main campus - Geneva, Switzerland.",
            ],
            program_name_degree: "Master of Science in Applied Artificial Intelligence",
            program_benefits_degree: [
                "A prestigious Master's degree (MSc AI) - AI Management Specialist - awarded by a long-standing business school in Switzerland.",
                "As a Swiss UMEF alumnus, the new Master's degree marks an important milestone on your path.",
                "Officially become a data & AI management expert - developing leadership skills and leveraging AI tools to drive positive changes in your organization.",
            ],
            link_iframe: "https://www.youtube.com/embed/mB0mDrgjVNs?si=wP6X9bDGqVVR2R28",
            listImgs: [
                "https://ideas.edu.vn/wp-content/uploads/2026/06/ltnumef10202501.webp",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSC_9177.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6555.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6740.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6777.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4193.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4314.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4298.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4268.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4255.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4240.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4215.jpg",
            ],
            level: "MBA",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/09/mscai.png.webp",
            name: "MSc AI",
            school: "Swiss UMEF",
            subjects: "<b>90</b> ECTS credits - <b>12</b> courses and thesis",
            duration: "18 months",
            country: "Switzerland",
            test: {
                high: [
                    "Leveraging AI in corporate management",
                    "Deep understanding of how modern enterprises are managed using AI",
                    "Interactive live classes, allowing discussions with professors and peers",
                    "Flexible tuition payment plans or installments",
                ],
            },
            experience: [
                "Bachelor's degree graduate",
                "Minimum 2 years of experience at managerial level",
                "Good conversational English or IELTS 6.0 equivalent",
            ],
            highlight: [
                "100% Online",
                "Master's Degree",
                "20:00 Tue - Thu (2-3h/session)",
                "2 sessions/course (Sunday)",
            ],
            tagline: "Swiss UMEF: First private university in Geneva to achieve the highest Swiss Federal Accreditation (SAC) - officially recognized by the Swiss Higher Education Council",
            link: "/en/mscai",
            description: "The MSc AI program is specially designed for business managers and leaders who want to explore and harness the potential of artificial intelligence in corporate management.",
            demographic: {
                jobs: [
                    { jobname: "Information Technology", percent: 40 },
                    { jobname: "Business Administration", percent: 30 },
                    { jobname: "Banking & Finance", percent: 12 },
                    { jobname: "Start-ups", percent: 10 },
                    { jobname: "Others", percent: 8 },
                ],
                ages: [
                    { jobname: "18 - 24", percent: 7 },
                    { jobname: "25 - 30", percent: 27 },
                    { jobname: "31 - 40", percent: 55 },
                    { jobname: "41 - 50", percent: 10 },
                    { jobname: "51+", percent: 1 },
                ],
            },
            degree: {
                back: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0002.webp",
                front: "https://ideas.edu.vn/wp-content/uploads/2025/03/UMEF-MSc-Degree.jpg",
                transcript: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0003.webp",
            },
            fee_plane: "4,400",
            fee_course: [
                {
                    name: "High Quality",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon5.png",
                    price: "11,900 CHF",
                    price_promo: "4,165 CHF",
                    benefits: [
                        "Installment support for 12 - 24 months via Sacombank.",
                        "Classes with foreign professors on weekday evenings (optional).",
                        "Technology application - AI Platform for learning developed by IDEAS.",
                        "Tài khoản truy cập tài liệu học tập, môn học theo chương trình gốc của trường.",
                        "Program assistants help remind homework deadlines, system support, and group chat connection.",
                        "Schedule: each course lasts 4 weeks, with 2 evening classes per week with foreign professors (expected Tue/Thu evenings, 2-3 hours each, 20:00 - 22:00).",
                        "Preliminary final exam evaluation: Evaluated and feedback given by the IDEAS academic council to ensure final assignments are on track, reducing fail rates."
                    ],
                },
            ],
            accreditation: [
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef1.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef3.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef4.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef5.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef2.png", link: "#" },
            ],
            require: [
                "Bachelor's degree and transcripts",
                "Curriculum Vitae (CV)",
                "Motivation Letter",
                "Passport photo (4×6)",
                "Valid passport copy",
                "English level equivalent to IELTS 6.0 or pass the entrance interview with UMEF Vietnam representative",
                "Admission Form <a href='https://ideas.edu.vn/wp-content/uploads/2025/03/ADMISSION-FORM_ENG_MSC_UMEF.docx' target='_blank' class='text_download'>(Download Here)</a>",
            ],
            faq: [
                {
                    q: "Can I apply for MSc AI without a computer science background?",
                    a: "Yes! You can apply for MSc AI even without a tech or computer science background. The Swiss UMEF MSc AI program is designed with an applied focus, suitable for managers, business owners, marketing specialists, and financial analysts looking to leverage AI in business. If you lack coding or data experience, the program provides bridging modules to familiarize you with AI tools without requiring deep technical knowledge. You will be guided step-by-step.",
                },
                {
                    q: "Does the program require special software background?",
                    a: "The Swiss UMEF MSc AI is designed for applications in business management. To ensure student readiness, IDEAS supports two bridging modules: Machine Learning & Deep Learning (understanding AI concepts) and AI Management (understanding how to apply AI in business operations). Therefore, regardless of your background, you will be able to follow and confidently apply AI.",
                },
                {
                    q: "Is it possible to study while working? Is the study load heavy?",
                    a: "Absolutely. The Swiss UMEF MSc AI program is structured flexibly for working professionals. Online learning allows you to self-schedule, but attendance in virtual classrooms is required (maximum 30% absence rate). Class times in the evening fit Vietnam time zones perfectly. IDEAS provides extra support via weekend workshops and case discussions to ease the workload.",
                },
            ],
            this_subjects: [
                { name: "Artificial Intelligence and Machine Learning", description: "Foundational AI and ML concepts, including supervised and unsupervised learning, artificial neural networks, deep learning models, and practical business data applications.", link: "", credit: 6 },
                { name: "Digital and Computer Vision", description: "Digital image processing and computer vision concepts, including feature extraction, object recognition, motion detection, and practical applications.", link: "", credit: 6 },
                { name: "Big Data Analytics", description: "Collecting, storing, processing, and analyzing big data. Working with tools like Hadoop, Spark, and big data analysis for strategic decisions.", link: "", credit: 6 },
                { name: "IoT", description: "Designing, controlling, and operating IoT systems in industrial, medical, and daily business operations.", link: "", credit: 6 },
                { name: "Reinforcement Learning and AI Optimization", description: "Reinforcement learning and optimization techniques in AI. Covers MDP, Q-learning, Deep Q-Networks, and applications in robotics, gaming, and finance.", link: "", credit: 6 },
                { name: "AI in Business Decision Making", description: "Applying AI in business decisions like finance, marketing, operations, and HR. Predictive analytics, automation, and organizational performance optimization.", link: "", credit: 6 },
                { name: "Economic Forecasting and AI-Driven Market Dynamics", description: "Economic forecasting and market analysis using AI. Applying machine learning models to analyze economic data, market behaviors, and support financial decisions.", link: "", credit: 6 },
                { name: "Financial Intelligence and Algorithmic Trading", description: "AI applications in finance and algorithmic trading. Building automated trading models, portfolio optimizations, and risk analysis using AI.", link: "", credit: 6 },
                { name: "AI Innovation and Entrepreneurship", description: "Developing AI-driven business ideas and innovations. Covers market opportunity detection, product development, and startup strategy with AI technologies.", link: "", credit: 6 },
                { name: "Advanced Project Management in AI", description: "Advanced project management skills in AI fields. Time, resource, and risk management, alongside Agile/Scrum methodologies for AI projects.", link: "", credit: 6 },
                { name: "Change management strategies for AI transition", description: "Strategies and skills for managing organizational changes when implementing AI. Covers leadership, communications, and overcoming resistance to ensure smooth transitions.", link: "", credit: 6 },
                { name: "Global AI policies and regulatory frameworks", description: "Global AI policies and regulations. Analyzing legal impacts on technological innovations and addressing legal challenges in setting AI guidelines.", link: "", credit: 6 },
                { name: "Capstone Project", description: "Comprehensive graduation project, where students apply learned skills to execute a practical AI project or compile an in-depth internship report.", link: "", credit: 18 },
            ],
        },
        IDEAS05: {
            benefits: [
                "Unique combination of modern business administration and pioneering no-code AI applications.",
                "100% flexible online learning taught by top Swiss professors and international experts.",
                "Supported by a 70% basic tuition scholarship from the Swiss UMEF and IDEAS partnership.",
                "Student network consisting of managers and executives in the digital era."
            ],
            program_name_degree: "MBA in Artificial Intelligence",
            program_benefits_degree: [
                "European standard Master of Business Administration specialized in AI from Swiss UMEF Switzerland.",
                "Master strategic leadership and execution capability for AI-driven digital transformation projects.",
                "No technical/coding background required, focusing on the practical application of AI models."
            ],
            link_iframe: "https://www.youtube.com/embed/mB0mDrgjVNs?si=wP6X9bDGqVVR2R28",
            listImgs: [
                "https://ideas.edu.vn/wp-content/uploads/2026/06/ltnumef10202501.webp",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSC_9177.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6555.jpg"
            ],
            level: "MBA",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/09/mba_program_icon_1778123208188.png",
            name: "MBA in AI",
            highlight: [
                "100% Online",
                "MBA with AI focus",
                "16-18 Months",
                "90 ECTS"
            ],
            school: "Swiss UMEF",
            country: "Switzerland",
            subjects: "<b>90</b> ECTS - <b>12</b> courses and Graduation Thesis",
            duration: "16-18 months",
            tagline: "Swiss UMEF: First private university in Geneva to achieve the highest Swiss Federal Accreditation (SAC) - officially recognized by the Swiss Higher Education Council",
            link: "/en/mbainai",
            experience: [
                "Bachelor's degree graduate",
                "Minimum 3 years of professional work experience",
                "English level equivalent to IELTS 6.0 or pass the entrance interview"
            ],
            fee_course: [
                {
                    name: "Standard",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon6.png",
                    price: "4,520 CHF",
                    benefits: [
                        "Includes 150 CHF admissions screening fee.",
                        "LMS Platform tuition fee 3,870 CHF (with 70% off applied).",
                        "Basic IDEAS academic support fee 500 CHF."
                    ]
                },
                {
                    name: "High Quality",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon5.png",
                    price: "7,970 CHF",
                    benefits: [
                        "Includes all benefits of the Standard package.",
                        "Auxiliary workshops, 1:1 thesis advising by IDEAS experts.",
                        "IDEAS LMS integrated with AI learning assistant.",
                        "Geneva graduation canton fee, Swiss consulate & degree legalization (300 CHF)."
                    ]
                }
            ],
            description: "The MBA in AI program from Swiss UMEF is the optimal integration of traditional business management theory and practical AI applications in business administration.",
            degree: {
                front: "https://ideas.edu.vn/wp-content/uploads/2025/11/z7191978013846_36a81ec39301d05fedaf6d4cd0293f9c-1.webp",
                back: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0002.webp"
            },
            require: [
                "Bachelor's degree and transcripts",
                "Curriculum Vitae (CV) with detailed experience",
                "Motivation Letter",
                "Passport photo (4x6)",
                "Valid passport copy",
                "Swiss UMEF Application Form"
            ],
            this_subjects: [
                { name: "Marketing Management", description: "Strategic marketing management in the digital era" },
                { name: "Human Capital and Talent Management", description: "Human resource management and talent development" },
                { name: "Entrepreneurship and Innovation", description: "Entrepreneurial spirit and driving innovation" },
                { name: "Corporate Finance", description: "Corporate finance and capital planning" },
                { name: "Accounting for Managers", description: "Accounting for managers to serve operations" },
                { name: "Digital Transformation", description: "Digital transformation strategy and change management" },
                { name: "Global Strategy", description: "Global business strategy in volatile environments" },
                { name: "Organizational Behaviour", description: "Organizational behavior and team performance optimization" },
                { name: "Leadership Development", description: "Strategic leadership capability development" },
                { name: "AI in Business Decision making", description: "Data & AI-driven business decision making" },
                { name: "Big Data Analysis", description: "Big data analysis for strategic orientation" },
                { name: "Project Management", description: "International standard professional project management" },
                { name: "Thesis", description: "MBA graduation thesis focusing on applied AI in business", credit: 18 }
            ]
        },
        IDEAS06: {
            pay_rule: `
        <img src="https://ideas.edu.vn/wp-content/new_public/data_imgs/icon2.png"/>
        <p><b>Pay in full or in 4 installments</b></p>
      <ul>
        <li><b><i class="fa-solid fa-file-invoice-dollar"></i> Students paying in full receive a 20% subsidy from IDEAS</b></li>
        <li><i class="fa-solid fa-file-invoice-dollar"></i> <b>Or pay in 4 installments: 40%, 20%, 20%, 20% </b></li>
        <li><br/></li>
        <li><b class="main_clr">Payment Methods</b></li>
        <li><i class="fa-solid fa-check"></i> Direct payment at IDEAS</li>
        <li><i class="fa-solid fa-check"></i> Online transfer via bank account details provided in the contract or via Payoo.</li>
        <li><i class="fa-solid fa-check"></i> IDEAS provides receipt confirmations (upon direct or card payment) within 1 working day. Weekend or holiday payments will be confirmed on the next working day. Collected tuition fees cannot be invoiced for VAT under Vietnamese regulations.</li>
        </ul>
      `,
            benefits: [
                "Delivers an outstanding competitive advantage, as DBA students do not only hold deep expertise but are also equipped with advanced executive skills.",
                "Stimulates academic thinking and intellectual development, training high-level strategic and analytical capabilities.",
                "Stand out in the crowd, advance your career by capturing the latest trends, modern theories, and contemporary issues in management and business.",
                "IDEAS supports: IDEAS-LMS system and auxiliary seminars on Sundays, providing homework guidance and preliminary final exam evaluations.",
            ],
            program_name_degree: "Dual DBA",
            program_benefits_degree: [
                "A DBA degree, the highest academic level, from a large, reputable business school with campuses spread across the world.",
                "The new Doctor of Business Administration from Ascencia Business School marks a very important milestone on your path.",
                "A true expert with the leadership skills needed to drive major changes in your field.",
            ],
            link_iframe: "https://ideas.edu.vn/wp-content/uploads/2025/10/Dual-DBA.webp",
            listImgs: [
                "https://i.ytimg.com/vi/2FtdKP2bf00/maxresdefault.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/ShareImage-1.jpg",
                "https://i.ytimg.com/vi/vJNu-g2vBg4/maxresdefault.jpg",
                "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT5c9vyalfHcxNNvOrudO4IQ9qGHz8PC0GhVw&s",
                "https://i.ytimg.com/vi/hOC3ISbaQdQ/maxresdefault.jpg",
                "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSCqFyt8JNQRbNF3Ut_mX_AVxvNgcviFQyxsQ&s",
            ],
            level: "DBA",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/10/Dual-DBA.webp",
            name: "Dual DBA",
            highlight: [
                "100% Online",
                "DBA",
                "Advisor-guided research",
                "2 sessions/course (Sunday)",
            ],
            school: "Estiam & RB College",
            country: "UK - France",
            subjects: "<b>4</b> stages | <b>2.5 - 3</b> years - 1 thesis",
            duration: "2.5 - 3 years",
            tagline: "Receive UK and French DBA degrees from Estiam and RB College",
            link: "/en/dual-dba-estiam-rb",
            experience: [
                "Master's degree and transcripts or equivalent.",
                "IELTS 6.0/TOEFL 60 certificate or other proof of English proficiency.",
            ],
            fee_plane: "4,400",
            fee_course: [
                {
                    name: "High Quality",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon5.png",
                    price: "12,500 Euro",
                    benefits: [
                        "Subsidy of <b class='main_clr'>3,000 EURO</b> for alumni when registering.",
                        "LMS support system: integrated I-AI chatbot to assist learning.",
                        "Research duration: 2.5 - 4 years.",
                        "Program assistants help remind deadlines, system support, and advisor connection.",
                        "Self-guided research with directions and mentoring from professors.",
                        "Preliminary final exam evaluation: Evaluated and feedback given by the IDEAS academic council to ensure final assignments are on track, reducing fail rates."
                    ],
                },
            ],
            description: "The Doctor of Business Administration is the terminal degree in business management. The program is designed for experienced professionals seeking to advance their strategic knowledge, research capability, and business leadership expertise.",
            demographic: {
                jobs: [
                    { jobname: "Business Administration", percent: 30 },
                    { jobname: "Banking & Finance", percent: 22 },
                    { jobname: "Information Technology", percent: 20 },
                    { jobname: "Start-ups", percent: 16 },
                    { jobname: "Others", percent: 12 },
                ],
                ages: [
                    { jobname: "18 - 24", percent: 7 },
                    { jobname: "25 - 30", percent: 27 },
                    { jobname: "31 - 40", percent: 55 },
                    { jobname: "41 - 50", percent: 10 },
                    { jobname: "51+", percent: 1 },
                ],
            },
            degree: {
                front: "https://ideas.edu.vn/wp-content/uploads/2025/10/Estiam-DBA-2.webp",
                back: "https://ideas.edu.vn/wp-content/uploads/2025/10/RB-DBA-2.webp",
            },
            accreditation: [
                { name: "Accreditation and Recognition", logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/DUAL-DEGREE-2.webp", link: "#" },
                { name: "Accreditation and Recognition", logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/DUAL-DEGREE-1.webp", link: "#" },
                { name: "Accreditation and Recognition", logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/DUAL-DEGREE-3.webp", link: "#" },
                { name: "Accreditation and Recognition", logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/DUAL-DEGREE-5.webp", link: "#" },
                { name: "Accreditation and Recognition", logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/DUAL-DEGREE.webp", link: "#" },
                { name: "Accreditation and Recognition", logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/DUAL-DEGREE4.webp", link: "#" },
                { name: "Accreditation and Recognition", logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/Erasmus_Logo.svg.webp", link: "#" },
            ],
            require: [
                "Personal documents: Scan of first passport page, Passport photo (4×6).",
                "Degrees and transcripts: Confirmation of Master's degree or equivalent.",
                "English level: IELTS 6.0/TOEFL 60 or other proof of English proficiency.",
                "Motivation letter: Around 250 words, detailing goals and reasons for choosing this program.",
                "Detailed CV: Outlining academic and professional experience.",
                "Recommendation letters: Two letters from schools or workplaces.",
                "Application Form <a href='https://ideas.edu.vn/wp-content/uploads/2025/10/Application-Form-.docx' target='_blank' class='text_download'>(Download Here)</a>",
            ],
            faq: [
                {
                    q: "Can I enroll in the Dual DBA without prior research experience?",
                    a: "Yes. The Dual DBA program is designed for managers and executives looking to enhance strategic thinking and applied research capabilities in business. IDEAS provides detailed guidance on research methodology, helping students step-by-step to build their DBA thesis without prior research background.",
                },
                {
                    q: "What if I am on a business trip and cannot submit my assignment on time?",
                    a: "The Dual DBA program is designed with flexibility to match students' busy schedules. Each course has a research period with the professor advisor. Assignment deadlines are usually set to Vietnam evening times. Students who arrange their schedule can manage. If you cannot submit on time, you can register for a paid <b>Resubmit</b> option, which opens another week for submission.",
                },
                {
                    q: "Can students participate in the graduation ceremony in Paris, France?",
                    a: "Yes. Students who complete the program can register to participate in the graduation ceremony in France, alongside other international students of Ascencia Business School. Ceremonies occur twice a year, in June and December. IDEAS will inform students regarding the school schedule for registrations.",
                },
            ],
            this_subjects: [
                { name: "Stage 1: Competitive Advantage", description: "<b>Around 12 months</b> <br/> Attend 6 seminar sessions (2-3 hours each) alongside 1:1 personal advising sessions. <br/> Draft a comprehensive thesis proposal.", link: "" },
                { name: "Stage 2: Advisor Professor Selection", description: "<b>Around 3 months</b> <br/> Select your research advisor from IDEAS or Estiam (fee applies). <br/> Finalize the outline and research plan. <br/> End of stage: Official registration of thesis topic.", link: "" },
                { name: "Stage 3: Under Direct Advisor Guidance (1:1)", description: "<b>Around 15 - 18 months</b> <br/> Submit periodic progress reports. <br/> Complete the full thesis of about 50,000 words (200-250 pages).", link: "" },
                { name: "Stage 4: Graduation Thesis Defense", description: "<b>Around 3 months</b> <br/> Finalize the thesis and defend it before the Thesis Academic Committee.", link: "" },
            ],
        },
        IDEAS07: {
            benefits: [
                "Interactive online sessions with foreign professors are mandatory. Enhances capability, communication confidence, discussion with professors, and practical business problem-solving.",
                "I-AI chatbot technology supports relevant content in the online MBA program managed by IDEAS. Program assistants help remind homework deadlines, system issues, and connections.",
                "IDEAS supports: IDEAS-LMS system and auxiliary seminars on Sundays, providing homework guidance and preliminary final exam evaluations.",
                "Graduation ceremony and academic study trip at the main campus - Geneva, Switzerland.",
            ],
            program_name_degree: "Bachelor of Business Administration",
            program_benefits_degree: [
                "A prestigious Bachelor's degree awarded by a long-standing and reputable university.",
                "As a Swiss UMEF alumnus, the new Bachelor's degree marks an important milestone on your path.",
                "Expand options to study Masters - Doctorates and develop your future career."
            ],
            link_iframe: "https://www.youtube.com/embed/ZrLeuFGGXQI?si=0tiJvbnRDzwEyo3B",
            listImgs: [
                "https://ideas.edu.vn/wp-content/uploads/2026/06/ltnumef10202501.webp",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSC_9177.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6555.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6740.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/11/DSCF6777.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4768.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4712.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4367.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4528.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4783.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4447.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4356.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4861.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4840.jpg",
                "https://ideas.edu.vn/wp-content/uploads/2025/03/NHP_4799.jpg",
            ],
            level: "BBA",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2026/02/TOPUP.webp",
            name: "TOP-UP BBA",
            school: "Swiss UMEF",
            subjects: "<b>60</b> ECTS credits - <b>10</b> courses and capstone",
            duration: "14-16 months",
            country: "Switzerland",
            experience: [
                "College or diploma graduate - Completed 2nd year of University",
                "Good conversational English or IELTS 6.0 equivalent",
            ],
            test: {
                high: [
                    "Interactive live classes, allowing discussions with professors and peers",
                    "Practical-oriented knowledge to solve executive-level management problems",
                    "Streamlined 10-course curriculum - no thesis requirement",
                    "Flexible tuition payment plans or installments",
                ],
                stand: [
                    "Flexible study and research schedule, supported by Vietnamese advisors during the course",
                    "Practical-oriented knowledge to solve executive-level management problems",
                    "Streamlined 10-course curriculum - no thesis requirement",
                    "Flexible tuition payment plans or installments",
                ],
            },
            highlight: [
                "100% Online",
                "Pathway to Bachelor's",
                "20:00 - 23:00 (Vietnam)",
                "2 sessions/course (Sunday)",
            ],
            tagline: "Swiss UMEF: First private university in Geneva to achieve the highest Swiss Federal Accreditation (SAC) - officially recognized by the Swiss Higher Education Council",
            link: "/en/bba",
            description: "A BBA Top-up pathway in 1 year of online study to get a Bachelor of Business Administration, don't let a bachelor's degree limit your strategic career path!",
            demographic: {
                jobs: [
                    { jobname: "Business Administration", percent: 30 },
                    { jobname: "Banking & Finance", percent: 22 },
                    { jobname: "Information Technology", percent: 20 },
                    { jobname: "Start-ups", percent: 16 },
                    { jobname: "Others", percent: 12 },
                ],
                ages: [
                    { jobname: "18 - 24", percent: 7 },
                    { jobname: "25 - 30", percent: 27 },
                    { jobname: "31 - 40", percent: 55 },
                    { jobname: "41 - 50", percent: 10 },
                    { jobname: "51+", percent: 1 },
                ],
            },
            degree: {
                back: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0002.webp",
                front: "https://ideas.edu.vn/wp-content/uploads/2026/06/bba-degree.webp",
                transcript: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0003.webp",
            },
            fee_course: [
                {
                    name: "TOP-UP BBA",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon5.png",
                    price: "3,000 CHF",
                    benefits: [
                        "12-month installment support via Sacombank.",
                        "Technology application - AI Platform for learning developed by IDEAS.",
                        "Classes with foreign professors on weekday evenings (optional).",
                        "Schedule: each course lasts 4 weeks, with 2 evening classes per week with foreign professors (expected Tue/Thu evenings, 2-3 hours each, 20:00 - 22:00).",
                        "Preliminary final exam evaluation: Evaluated and feedback given by the IDEAS academic council to ensure final assignments are on track, reducing fail rates."
                    ],
                },
            ],
            accreditation: [
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef1.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef3.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef4.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef5.png", link: "#" },
                { name: "Accreditation UMEF", logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef2.png", link: "#" },
            ],
            require: [
                "College diploma or university 2nd-year transcript.",
                "Curriculum Vitae (CV).",
                "Motivation Letter.",
                "Passport photo (4×6).",
                "Valid passport copy.",
                "English level equivalent to IELTS 6.0 or pass the entrance interview with UMEF Vietnam representative.",
                "Refer to PRE-TOP UP program if you do not yet meet requirements <a href='#' target='_blank' class='text_download'>PRE TOP-UP</a>",
            ],
            faq: [
                {
                    q: "Does the Top-up program require a thesis defense?",
                    a: "No. The Swiss UMEF Top-up BBA program comprises 10 courses and does not require a thesis defense, only a Capstone Project focusing on raising practical management skills.",
                },
                {
                    q: "Where is the graduation ceremony organized?",
                    a: "Upon completing the Top-up BBA, students can participate in the graduation ceremony organized by Swiss UMEF. Typically, the graduation ceremony takes place in Geneva, Switzerland, where the main campus is located. However, Swiss UMEF can also organize graduation ceremonies in Vietnam or other locations depending on student enrollment and conditions. Students can choose to attend the ceremony in Geneva or in Vietnam organized in coordination with IDEAS.",
                },
                {
                    q: "Can I pursue a Master's after completing the Top-up BBA?",
                    a: "Yes, after completing the Swiss UMEF Top-up BBA program, students can continue to study for a Master of Business Administration (MBA) at suitable universities or institutions.",
                },
            ],
            this_subjects: [
                { name: "Introduction to Management", description: "Exploring advanced topics, strategic marketing planning, external and internal environment analysis, consumer behavior, market structure, and new product development.", link: "", credit: 6 },
                { name: "Introduction to Finance", description: "Managerial orientation of HR activities (recruitment, selection, training, rewards, and performance appraisal) and contemporary issues in talent management.", link: "https://youtu.be/CebD5PCML6w?si=qbXiR8r9eztjvUvM", credit: 6 },
                { name: "Organisational Behaviour", description: "Principles and practices of entrepreneurship, identifying opportunities, building business models, and strategic launch of new ventures.", link: "https://youtu.be/t1g7aCRoC-I?si=5luqjqunPivUr5wL", credit: 6 },
                { name: "Global Marketing", description: "Investment appraisals, compound interest, net present value, internal rate of return, capital budgeting, portfolio theory, and CAPM.", link: "https://youtu.be/zk1-2CERWHs?si=tT-pLAUoA5iI_b3h", credit: 6 },
                { name: "AI in Business", description: "Understanding financial statements, cost accounting, manager-focused accounting tools, and analytical tools for strategic decisions.", link: "", credit: 6 },
                { name: "Project Management", description: "Process of digitalization, strategic response to technology, changes in business models, and assessing new tech opportunities.", link: "", credit: 6 },
                { name: "Innovation & Design Thinking", description: "Competitive analysis, strategic formulation, positioning, defining firm boundaries, and maximizing long-term profit in uncertain markets.", link: "https://youtu.be/wVY3uLMG-Fk?si=rTdwcjAk6-tEgbqS", credit: 6 },
                { name: "Total Quality Management", description: "Fundamentals of project management, portfolio selection, risk handling, cost and schedule controls, and project termination.", link: "https://youtu.be/MMYVUtpiAPk?si=R1XbXLyILON4-yK6", credit: 6 },
                { name: "Change Management", description: "Human behavior in organizations, corporate culture, team dynamics, motivation, power and politics, and organization structure.", link: "", credit: 6 },
                { name: "Management Information Systems", description: "Cross-cultural leadership, diversity, organizational interactions, strategic vision, and resolving conflicts in multicultural settings.", link: "", credit: 6 },
                { name: "Capstone Project", description: "Synthesis capstone project.", link: "", credit: 6 },
            ],
        },
    },
    graduation_ceremony: [
        {
            title: "Graduation Ceremony",
            location: "Ho Chi Minh City",
            avatar: "./assets/ltn27122025.webp",
            school: "Swiss UMEF",
            name: "MBA/EMBA",
            time: "27/12/2025",
            link: "https://www.facebook.com/ideas.edu.vn/posts/pfbid034nzCDGcFVfz54M62b4Yod9iJ3mMx2eVNMXB33PpDeDSw6Xw1cZsH4oucpX2TogDcl?locale=vi_VN",
        },
        {
            title: "Graduation Ceremony",
            location: "Geneva - Switzerland",
            avatar: "./assets/ltnumef10202501.webp",
            school: "Swiss UMEF",
            name: "MBA/EMBA/MSc AI",
            time: "29/10/2025",
            link: "http://ideas.edu.vn/chuyen-di-thang-10-2025",
        },
        {
            title: "Graduation Ceremony",
            location: "Eden Star Hotel - HCMC",
            avatar: "./assets/ltn72025.webp",
            school: "Ascencia Business School",
            name: "Global MBA - DBA",
            time: "26/07/2025",
            link: "./assets/ltn72025.webp",
        },
        {
            title: "Graduation Ceremony",
            location: "Paris - France",
            avatar: "./assets/quangnon_cdp.webp",
            school: "Ascencia Business School",
            name: "Global MBA - DBA",
            time: "02/07/2025",
            link: "https://www.facebook.com/ideas.edu.vn/posts/pfbid02imDSb2CKDPVgKCQtkQTfihwpayYVVjuunvfDfRWfxTMstTD661BnZmYygnTwt9wpl?locale=vi_VN",
        },
        {
            title: "Graduation Ceremony",
            location: "IDEAS - Vietnam",
            avatar: "./assets/8X1A9328-1-1.jpg",
            school: "Ascencia Business School",
            name: "Global MBA - DBA",
            time: "23/11/2024",
            link: "https://youtu.be/hmVxOq5jkeM?si=gR-YOgFi2KQJftr9",
        },
        {
            title: "Graduation Ceremony",
            location: "IDEAS - Vietnam",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2024/10/Totnghiepumef.jpg",
            school: "Swiss UMEF",
            name: "EMBA & Online MBA",
            time: "26/10/2024",
            link: "https://youtu.be/fBf5YcaMxDY?si=eJDfqKWc4HxT_TmS",
        },
        {
            title: "Graduation Ceremony",
            location: "Paris - France",
            avatar: "https://ideas.edu.vn/wp-content/new_public/data_imgs/image.png",
            school: "Ascencia Business School",
            name: "Global MBA - DBA",
            time: "18/07/2024",
            link: "https://www.facebook.com/share/r/14UHtxBTZQ/",
        },
        {
            title: "Graduation Ceremony",
            location: "IDEAS - Vietnam",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2024/01/416256674_837845658141991_5379123310787471174_n.jpg",
            school: "Ascencia Business School",
            name: "Global MBA - DBA",
            time: "06/01/2024",
            link: "https://youtu.be/Dc78ClToNRo?si=kfg00KZ6gYpOWwTI",
        },
    ],
    student_quote: [
        {
            name: "Nguyễn Thanh Bình",
            title: "French Doctor of Business Administration (DBA) – Ascencia Business School - Director of Information & Environment technology application Institute",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/02/casc1.jpg",
            content: "Thank you for the very enthusiastic support of the IDEAS academic and administrative staff. They have accompanied us throughout the research journey, supporting day and night to help us complete our Doctorate objectives – the terminal academic degree.",
        },
        {
            name: "Nguyễn Huỳnh Phương",
            title: "French Master of Business Administration (Global MBA) – Ascencia Business School - UNIT MANAGER in Hanwha Life Vietnam",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/02/huynhphuong.jpg",
            content: "For those choosing online study, I recommend selecting a trustworthy institution like IDEAS. You should share any stress or difficulties with professors because they will give helpful suggestions to help you overcome them.",
        },
        {
            name: "Nguyễn Thị Hà Miên",
            title: "French Master of Business Administration (Global MBA) – Ascencia Business School - Deputy Project Manager",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/02/hamien.jpg",
            content: "I chose the online program because it is more flexible. In addition, the 24/7 support of IDEAS helped me complete assignments on time and kept me updated with constant class reminders.",
        },
        {
            name: "Lê Ngọc Thương",
            title: "Swiss Master of Business Administration (Executive MBA) – Swiss UMEF Head of Commercial Operations – Boehringer Ingelheim",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/02/cumef.jpg",
            content: "The program connected me with many classmates across different industries, helping me gather valuable shares and experiences. I am especially grateful to IDEAS for their support over the past year, from expert insights to 24/7 admin assistance, helping me balance work and study to achieve my goal.",
        },
        {
            name: "Lê Chí Thành",
            title: "French Doctor of Business Administration (DBA) – Ascencia Business School - Channel Business Manager (Indochina) | Leica Biosystems (an OpCo of Danaher)",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/02/casc2.jpg",
            content: "My online learning journey since the 2016 MBA program and then continuing with the DBA has always been accompanied and supported by the IDEAS team. Online learning was the right choice for me to balance work and family. My deep gratitude goes to Dr. Pham Quang Vinh and the IDEAS support team for constantly encouraging me along this long path.",
        },
        {
            name: "Chu Hoàng Thái",
            title: "Swiss Master of Business Administration (Executive MBA) – Swiss UMEF Director Of Housekeeping – REGENT PHU QUOC",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/02/chu_hoang_thai.jpg",
            content: "Choosing the EMBA from Swiss UMEF supported by IDEAS was a great decision for me, as it was highly convenient to study while working, and fitted my financial capacity. Besides the knowledge from Swiss and European professors, the greatest value for me has been the networking.",
        },
    ],
    school: {
        "Swiss UMEF": {
            link: "https://www.swiss-umef.ch/en",
            logo: "https://ideas.edu.vn/wp-content/uploads/2025/07/2-MBA-European-Online-Ranking-1.webp",
            small_logo: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcShSoIjIIW_XlfCq3nbCxt--s3zt2lxrO74_A&amp;s",
        },
        "Ascencia Business School": {
            link: "https://www.ascencia-business-school.com/en/",
            logo: "https://ideas.edu.vn/wp-content/uploads/2024/03/Logo-Ascencia-Business-School-1.png",
            small_logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/Ascencia-Favicon.png",
        },
        "College de Paris": {
            link: "https://www.collegedeparis.fr/",
            logo: "https://www.collegedeparis.fr/wp-content/uploads/2021/06/cdp-formation-continue.png",
            small_logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/Ascencia-Favicon.png",
        },
        "Estiam & RB College": {
            link: "https://www.estiam.education/",
            logo: "https://ideas.edu.vn/wp-content/uploads/2025/03/estiam.png",
            small_logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/small_estiam.webp",
        },
    },
    partner: [
        {
            name: "ChiefAI",
            link: "#",
            logo: "https://chiefaiofficer.vn/wp-content/uploads/2024/09/cao-logo.png",
        },
        {
            name: "ChiefAI",
            link: "#",
            logo: "https://ideas.edu.vn/wp-content/uploads/2023/07/tssac-2.webp",
        },
        {
            name: "ChiefAI",
            link: "#",
            logo: "https://ideas.edu.vn/wp-content/uploads/2023/07/Untitled-design-1-e1657270193979.webp",
        },
        {
            name: "ChiefAI",
            link: "#",
            logo: "https://ideas.edu.vn/wp-content/uploads/2023/07/1646029406269.webp",
        },
        {
            name: "ChiefAI",
            link: "#",
            logo: "https://ideas.edu.vn/wp-content/uploads/2025/03/estiam.png",
        },
    ],
    faq: [
        {
            q: "Is this a joint training program between IDEAS and the Universities?",
            a: "<b>No,</b> <br/>When you start a program, you will officially be a student of the respective university. IDEAS does not directly run joint training but serves as a bridge, academic advisor, and local support entity throughout your studies and degree legalization process. <br/> <br/> IDEAS helps students access <b>official programs</b> from international universities, supports them throughout their studies, and helps resolve any issues to ensure the smoothest learning experience. <br/> The entire program, degrees, and transcripts are issued directly by the universities and can be **legalized by the Vietnam Embassy** in the country where the university's main campus resides.",
        },
        {
            q: "Does IDEAS organize offline classes or only support online study?",
            a: "The programs supported by IDEAS are primarily delivered online, allowing students flexibility in time and location. However, IDEAS also frequently organizes offline workshops, academic seminars, and networking events for students to connect and exchange knowledge.",
        },
        {
            q: "How are the IDEAS auxiliary seminars organized and are they mandatory?",
            a: "The auxiliary seminars by IDEAS are organized online and led by instructors or industry experts. The seminar content focuses on supplementing course materials, answering questions, deep-diving into subjects, and applying concepts in practice. These seminars are optional and do not affect course grades. However, IDEAS strongly encourages students to participate to enhance knowledge and achieve better academic outcomes. It is also an excellent opportunity to network with peers who are managers and leaders in the same cohort.",
        },
    ],
};
