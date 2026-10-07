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
                "Chương trình học tập Cử nhân  3 năm (180 ECTS) chất lượng cao từ Thụy Sĩ.",
                "Học trực tuyến 100% linh hoạt trên platform hiện đại, phù hợp cho người đi làm và học sinh tốt nghiệp THPT.",
                "IDEAS hỗ trợ: hệ thống IDEAS - LMS, trợ lý AI học tập, các lớp chuyên đề bổ trợ cuối tuần.",
                "Lễ tốt nghiệp trang trọng tại Geneva, Thụy Sĩ và cơ hội tham quan các nước Châu Âu."
            ],
            program_name_degree: "Bachelor of Business Administration",
            program_benefits_degree: [
                "Tấm bằng Cử nhân  chuẩn Châu Âu được cấp bởi trường Đại học Swiss UMEF danh giá tại Thụy Sĩ.",
                "Nền tảng kiến thức quản trị kinh doanh toàn diện và khả năng hội nhập quốc tế.",
                "Cơ hội liên thông trực tiếp lên các chương trình Thạc sĩ (MBA, MSc AI) chuẩn quốc tế."
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
                "Trực tuyến 100%",
                "BBA ",
                "36 tháng (3 năm)",
                "180 ECTS"
            ],
            school: "Swiss UMEF",
            country: "Thụy Sĩ",
            subjects: "<b>180</b> ECTS - <b>34</b> môn học và Luận văn tốt nghiệp",
            duration: "36 tháng",
            tagline: "Swiss UMEF: Đại học tư thục đầu tiên tại Geneva đạt Kiểm định Liên bang cao nhất Thụy Sĩ (SAC) - công nhận chính thức bởi Hội đồng Giáo dục Thụy Sĩ",
            link: "/fullbba",
            experience: [
                "Tốt nghiệp THPT hoặc tương đương",
                "Đạt yêu cầu xét tuyển hồ sơ của Hội đồng tuyển sinh",
                "Tiếng Anh đạt chuẩn đầu vào hoặc tham gia phỏng vấn đánh giá năng lực"
            ],
            fee_course: [
                {
                    name: "Standard",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon6.png",
                    price: "8.050 CHF (Trọn gói 3 năm)",
                    benefits: [
                        "Học phí cơ bản đã áp dụng học bổng 80% từ quỹ hỗ trợ.",
                        "Tài khoản học tập LMS gốc của Swiss UMEF.",
                        "Hỗ trợ thủ tục nhập học và hồ sơ học viên tại Việt Nam."
                    ]
                }
            ],
            description: "Chương trình Cử nhân Quản trị Kinh doanh (Full BBA) 3 năm từ Swiss UMEF mang đến cho người học nền tảng kiến thức kinh doanh toàn diện chuẩn quốc tế, giúp mở rộng cơ hội sự nghiệp và chuẩn bị vững chắc cho bậc học cao hơn.",
            degree: {
                front: "https://ideas.edu.vn/wp-content/uploads/2026/06/bba-degree.webp",
                back: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0002.webp"
            },
            require: [
                "Bằng tốt nghiệp THPT và học bạ THPT",
                "Sơ yếu lý lịch (CV)",
                "Thư động lực (Motivation Letter)",
                "Ảnh hộ chiếu (4x6)",
                "Passport còn hạn",
                "Application Form của Swiss UMEF"
            ],
            this_subjects: [
                { name: "Rhetoric and Composition", description: "Kỹ năng viết học thuật và trình bày văn bản chuyên nghiệp", credit: 5 },
                { name: "Financial Mathematics", description: "Toán tài chính ứng dụng trong mô hình hóa kinh doanh", credit: 5 },
                { name: "Introduction to Marketing", description: "Khái niệm và nguyên lý marketing căn bản", credit: 5 },
                { name: "Economics", description: "Kinh tế học vi mô và vĩ mô nền tảng", credit: 5 },
                { name: "Digital Marketing", description: "Ứng dụng công nghệ số vào tiếp thị và quảng cáo", credit: 5 },
                { name: "Critical Thinking", description: "Tư duy phản biện và giải quyết vấn đề logic", credit: 5 },
                { name: "Statistics Applied to Business", description: "Thống kê ứng dụng phân tích dữ liệu kinh doanh", credit: 5 },
                { name: "Human Resource Management", description: "Quản trị nguồn nhân lực trong tổ chức", credit: 5 },
                { name: "Corporate Finance", description: "Tài chính doanh nghiệp cơ bản và dòng tiền", credit: 5 },
                { name: "International Business", description: "Kinh doanh quốc tế và các rào cản thương mại", credit: 5 },
                { name: "Business Law", description: "Luật kinh doanh và các cam kết pháp lý", credit: 5 },
                { name: "Communication Strategy", description: "Chiến lược truyền thông nội bộ và ngoại giao", credit: 5 },
                { name: "Managerial Accounting", description: "Kế toán quản trị phục vụ ra quyết định nội bộ", credit: 5 },
                { name: "International Management", description: "Quản trị đa quốc gia và điều hành chuỗi giá trị", credit: 5 },
                { name: "Risk Management", description: "Quản trị rủi ro tài chính và vận hành", credit: 5 },
                { name: "Entrepreneurship and Small Business Management", description: "Khởi nghiệp và quản lý doanh nghiệp nhỏ thực chiến", credit: 5 },
                { name: "Business Negotiation", description: "Nghệ thuật đàm phán và thuyết phục trong kinh doanh", credit: 5 },
                { name: "Purchasing and Supply Chain", description: "Quản trị mua hàng và chuỗi cung ứng toàn cầu", credit: 5 },
                { name: "Leadership and Team Building", description: "Kỹ năng lãnh đạo và xây dựng đội ngũ hiệu quả", credit: 5 },
                { name: "Marketing of Luxury Products", description: "Tiếp thị sản phẩm cao cấp và định vị phân khúc", credit: 5 },
                { name: "Business Policy and Strategy", description: "Chính sách doanh nghiệp và quản trị chiến lược", credit: 5 },
                { name: "Geopolitics and Geo-Economics", description: "Địa chính trị và địa kinh tế tác động toàn cầu", credit: 5 },
                { name: "Sustainable Development", description: "Phát triển bền vững và trách nhiệm xã hội ESG", credit: 5 },
                { name: "Thesis Methodology", description: "Phương pháp luận nghiên cứu khoa học viết khóa luận tốt nghiệp", credit: 5 },
                { name: "Introduction to Management", description: "Nhập môn khoa học quản trị hiện đại", credit: 5 },
                { name: "Global Marketing", description: "Marketing toàn cầu và thâm nhập thị trường quốc tế", credit: 5 },
                { name: "Organizational Behaviour", description: "Hành vi tổ chức và văn hóa doanh nghiệp", credit: 5 },
                { name: "Project Management and Decision Making", description: "Quản trị dự án và ra quyết định dựa trên dữ liệu", credit: 5 },
                { name: "Introduction to Financial Accounting", description: "Kế toán tài chính cơ bản và báo cáo tài chính", credit: 5 },
                { name: "Total Quality Management", description: "Quản trị chất lượng toàn diện trong doanh nghiệp", credit: 5 },
                { name: "Innovation Management", description: "Quản trị đổi mới sáng tạo thúc đẩy tăng trưởng", credit: 5 },
                { name: "Change Management", description: "Quản trị sự thay đổi và tái cấu trúc doanh nghiệp", credit: 5 },
                { name: "Management Information Systems", description: "Hệ thống thông tin quản lý tối ưu hóa quy trình số", credit: 5 },
                { name: "AI in Business", description: "Ứng dụng trí tuệ nhân tạo nâng cao năng suất kinh doanh", credit: 5 },
                { name: "Dissertation", description: "Khóa luận tốt nghiệp cử nhân BBA nghiên cứu ứng dụng thực tế", credit: 15 }
            ]
        },
        IDEAS02: {
            benefits: [
                "Bắt buộc các lớp học tương tác trực tuyến với giảng viên nước ngoài. Nâng cao khả năng, tự tin giao tiếp, trao đổi với Giáo sư và giải quyết các vấn đề thực tiễn trong doanh nghiệp.",
                "Công nghệ chatbox I-AI hỗ trợ các nội dung phù hợp trong chương trình MBA online do IDEAS quản lý. Trợ lý chương trình hỗ trợ nhắc nhở deadline bài tập, các vấn đề liên quan đến hệ thống, kết nối.",
                "IDEAS hỗ trợ: hệ thống IDEAS - LMS & lớp chuyên đề bổ trợ vào các ngày Chủ nhật, có hướng dẫn bài tập và đánh giá sơ bộ bài Final exam.",
                "Lễ tốt nghiệp và chuyến đi học tập tại trụ sở chính của trường - Geneva Thụy Sĩ.",
            ],
            program_name_degree: "Master of Business Administration",
            program_benefits_degree: [
                "Tấm bằng Thạc sĩ danh giá được trao từ một trường đại học lâu đời và danh tiếng.",
                "Là cựu học viên của trường Swiss UMEF, tân Thạc Sĩ đánh dấu cột mốc quan trọng trên con đường phát triển.",
                "Chính thức trở thành chuyên gia - phát triển các kỹ năng lãnh đạo cần thiết để thúc đẩy sự thay đổi tích cực trong lĩnh vực của bạn.",
            ],
            link_iframe:
                "https://www.youtube.com/embed/ZrLeuFGGXQI?si=o8eCXmmewBzKvCgT",
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
            avatar:
                "https://ideas.edu.vn/wp-content/uploads/2025/09/online-mba-1.png.webp",
            name: "Online MBA",
            school: "Swiss UMEF",
            subjects: "<b>90</b> tín chỉ ECTS - <b>12</b> môn và luận văn",
            duration: "18 tháng",
            country: "Thuỵ Sĩ",
            experience: [
                "Tốt nghiệp cử nhân",
                "Tối thiểu 3 năm kinh nghiệm làm việc",
                "Tiếng Anh giao tiếp tốt hoặc bằng cấp tương đương IELTS 6.0",
            ],
            test: {
                high: [
                    "Lớp học tương tác trực tiếp, được trao đổi và thảo luận cùng Giảng viên và học viên",
                    "Kiến thức quản trị chuyên sâu, kết hợp với case study thực tiễn",
                    "Lộ trình học lên DBA với nhiều học bỗng từ trường và IDEAS",
                    "Học phí linh hoạt chia nhỏ hoặc trả góp",
                ],
                stand: [
                    "Thời gian học tập, nghiên cứu tự sắp xếp linh hoạt, hỗ trợ bởi các giảng viên Việt Nam trong quá trình học",
                    "Kiến thức quản trị chuyên sâu, kết hợp với case study thực tiễn",
                    "Lộ trình học lên DBA với nhiều học bỗng từ trường và IDEAS",
                    "Học phí linh hoạt chia nhỏ hoặc trả góp",
                ],
            },
            highlight: [
                "Trực tuyến 100%",
                "Thạc sĩ (Master)",
                "20:00 - 23:00 (Vietnam)",
                "2 buổi chuyên đề/môn (Chủ nhật)",
            ],
            tagline:
                "Swiss UMEF: Đại học tư thục đầu tiên tại Geneva đạt Kiểm định Liên bang cao nhất Thụy Sĩ (SAC) - công nhận chính thức bởi Hội đồng Giáo dục Thụy Sĩ",
            link: "/mba",
            description:
                "Chương trình MBA Online phù hợp cho người bận rộn. Bằng cấp được công nhận bởi Hội đồng Kiểm định Liên bang Thụy Sĩ, đảm bảo giá trị quốc tế, mang đến kiến thức thực tiễn và cập nhật theo xu hướng kinh doanh toàn cầu.",
            demographic: {
                jobs: [
                    {
                        jobname: "Quản trị kinh doanh",
                        percent: 30,
                    },
                    {
                        jobname: "Ngân hàng",
                        percent: 22,
                    },
                    {
                        jobname: "Công nghệ thông tin",
                        percent: 20,
                    },
                    {
                        jobname: "Start-up",
                        percent: 16,
                    },
                    {
                        jobname: "Khác",
                        percent: 12,
                    },
                ],
                ages: [
                    {
                        jobname: "18 - 24",
                        percent: 7,
                    },
                    {
                        jobname: "25 - 30",
                        percent: 27,
                    },
                    {
                        jobname: "31 - 40",
                        percent: 55,
                    },
                    {
                        jobname: "41 - 50",
                        percent: 10,
                    },
                    {
                        jobname: "51+",
                        percent: 1,
                    },
                ],
            },

            degree: {
                back: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0002.webp",
                front:
                    "https://ideas.edu.vn/wp-content/uploads/2025/11/z7191978013846_36a81ec39301d05fedaf6d4cd0293f9c-1.webp",
                transcript:
                    "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0003.webp",
            },
            fee_plane: "3,000",
            fee_course: [
                // {
                //   name: "Standard",
                //   icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon6.png",
                //   price: "2.900 CHF",
                //   benefits: [
                //     "<b>Dành cho học viên đăng ký từ 01/08/2025:</b> Trợ phí từ trường giảm <b>500 CHF</b>.",
                //     "Tài khoản truy cập tài liệu học tập, môn học theo chương trình gốc của trường.",
                //     "Trợ lý chương trình nhắc deadline, hỗ trợ hệ thống, kết nối giảng viên qua Group Zalo/Email.",
                //     "I-AI: Chatbot hỗ trợ nội dung và giải đáp thắc mắc (IDEAS).",
                //     "Lớp chuyên đề của IDEAS tổ chức 2 ngày Chủ nhật vào tuần thứ 2 và thứ 4 mỗi môn, từ 8h30 - 11h30 và 13h30 - 16h30, nhằm giúp kiểm tra và hướng dẫn hoàn thành bài Final Exam.",
                //   ],
                // },
                {
                    name: "High Quality",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon5.png",
                    price: "9.900 CHF",
                    // price_promo: "4.675 CHF",
                    benefits: [
                        "Hỗ trợ trả góp 12 - 24 tháng qua Sacombank",
                        "Bao gồm chương trình Standard và Hệ thống học tập eAcademy.",
                        "Ứng dụng công nghệ - Platform Ai cho học tập do IDEAS phát triển",
                        "Lớp học GVNN vào các buổi tối trong tuần (tùy chọn)",
                        "Lịch học: mỗi môn học kéo dài 04 tuần, mỗi tuần có 02 buổi tối học với giảng viên nước ngoài của trường (lịch dự kiến rơi vào tối thứ 3 và tối thứ 5, mỗi buổi từ 2-3 tiếng, từ 20h00 - 22h00)",
                        "Đánh giá sơ bộ bài Final: Được hội đồng chuyên môn của IDEAS đánh giá, góp ý bài Final đã đi đúng hướng, tránh lạc đề, học viên nắm được điểm đánh giá sơ bộ và hạn chế được khả năng rớt môn.",
                    ],
                },
            ],

            accreditation: [
                {
                    name: "Kiểm định trường UMEF",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef1.png",
                    link: "#",
                },
                {
                    name: "Kiểm định trường UMEF",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef3.png",
                    link: "#",
                },
                {
                    name: "Kiểm định trường UMEF",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef4.png",
                    link: "#",
                },
                {
                    name: "Kiểm định trường UMEF",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef5.png",
                    link: "#",
                },
                {
                    name: "Kiểm định trường UMEF",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef2.png",
                    link: "#",
                },
            ],
            require: [
                "Bằng cử nhân và bảng điểm",
                "Sơ yếu lí lịch (CV)",
                "Thư động lực (Motivation Letter)",
                "Ảnh hộ chiếu (4×6)",
                "Passport",
                "Tiếng anh tương đương IELTS 6.0 hoặc có thể phỏng vấn đầu vào với đại diện của trường tại Việt Nam",
                "Admission Form <a href='https://ideas.edu.vn/wp-content/uploads/2025/03/ADMISSION-FORM_ENG_MBA-_UMEF.docx' target='_blank' class='text_download'>(Download Here)</a>",
            ],
            faq: [
                {
                    q: "Luận văn của chương trình MBA yêu cầu như thế nào",
                    a: "Đối với chương trình MBA của Swiss UMEF, học viên bắt buộc phải thực hiện luận văn tốt nghiệp để hoàn thành chương trình. Luận văn là một nghiên cứu chuyên sâu về một vấn đề quản trị thực tiễn, giúp học viên ứng dụng các kiến thức đã học vào giải quyết một vấn đề cụ thể trong doanh nghiệp hoặc ngành nghề của mình. <br/>  <br/> Học viên sẽ được IDEAS hướng dẫn tập trung hoặc hướng dẫn 1-1 để xây dựng đề cương, thu thập dữ liệu, phân tích và trình bày kết quả. Luận văn cần tuân thủ các tiêu chuẩn học thuật về nội dung, hình thức và phương pháp nghiên cứu theo quy định của Swiss UMEF. Tối thiểu bài luận văn cần 20,000 từ, theo chuẩn APA format. Sau khi hoàn thành, luận văn sẽ được hội đồng thẩm định đánh giá.",
                },
                {
                    q: "Lễ tốt nghiệp sau khi hoàn thành Online MBA sẽ được tổ chức ở đâu, có thể qua Trường không?",
                    a: "Sau khi hoàn thành chương trình Online MBA, học viên sẽ được tham gia lễ tốt nghiệp do Swiss UMEF tổ chức. Thông thường, lễ tốt nghiệp sẽ diễn ra tại Geneva, Thụy Sĩ, nơi đặt trụ sở chính của Trường. Tuy nhiên, Swiss UMEF cũng có thể tổ chức buổi lễ tốt nghiệp tại Việt Nam hoặc các địa điểm khác tùy theo số lượng học viên và điều kiện tổ chức. Học viên có thể lựa chọn tham dự lễ tốt nghiệp tại Geneva hoặc tại địa điểm do IDEAS phối hợp tổ chức.",
                },
                {
                    q: "Bằng MBA của Swiss UMEF có giá trị như thế nào?",
                    a: "Bằng MBA do Swiss UMEF (Thụy Sĩ) cấp, có giá trị quốc tế. Swiss UMEF là một trường đại học tư nhân đầu tiên tại Geneva có kiểm định Liên Bang và được công nhận tại nhiều quốc gia. Phù hợp với những người muốn nâng cao kiến thức quản trị, phát triển sự nghiệp ở môi trường quốc tế.",
                },
            ],
            this_subjects: [
                {
                    name: "MBA 400. Marketing Management",
                    description:
                        "This course progressively builds on marketing issues by exploring more advanced topics and strategic planning in the marketing concept. Our attention will be drawn to laying the groundwork by strategically planning all the marketing approaches and analyzing the external and internal environment of the organization. <br/><br/> This course also examines the dynamic environment inhabited by today's marketers, helping students understand the marketplace and integrate the appropriate information into marketing decisions. The course focuses on an integrated approach and strategy-based actions, covering only those critical and advanced issues required to succeed in future professional work. <br/><br/> Additionally, our focus includes the concept of marketing, the marketing manager's job, the development of a marketing strategy, marketing research, consumer behavior and analysis, organizational buying behavior; market structure and competitor analysis; marketing mix decision-making; communications and advertising strategy; channels of distribution; the personal sales channel; pricing; sales promotion; strategies for service markets; strategies for technology-based markets; global marketing strategies; and new product development",
                    link: "https://youtu.be/KQ6NgVvctXc?feature=shared",
                    credit: 6,
                },
                {
                    name: "MBA 401. Human Capital and Talent Management",
                    description:
                        "This course is designed to provide a fresh approach to management students, based on dynamic, real-life organizational events confronting both human resource managers and line managers who often implement personnel programs and policies. <br/><br/> Human Resource Management takes a managerial orientation and is viewed as being relevant to managers in every unit, project or team. Managers are constantly faced with human relation management issues, problems and decision making. <br/><br/>  This course will introduce management students to HR activities (recruitment and selection, training and development, reward management and performance appraisal) with the emphasis that it is the bundles of HR interventions and not single practices that develop effective human resources. Based on realistic, current trends confronting managers, this course will also expose the management students to contemporary issues, such as, employee engagement and organizational change, and how HR can work together with knowledge management, technology, internationalization - and talent management - to further develop the effectiveness of human resources and help create, develop and sustain a high-performing organization",
                    link: "https://youtu.be/16dkyS-LVes?feature=shared",
                    credit: 6,
                },
                {
                    name: "MBA 402. Entrepreneurship and Innovation",
                    description:
                        "This course explores the principles and practices of entrepreneurship and innovation. Students will learn how to identify and evaluate entrepreneurial opportunities, develop innovative ideas, and create viable business models. The course emphasizes critical thinking, creativity, and problem-solving skills necessary for successful entrepreneurship. Students will also gain an understanding of the challenges and strategies involved in launching and growing a new venture in a dynamic business environment",
                    link: "",
                    credit: 6,
                },
                {
                    name: "MBA 403. Corporate Finance",
                    description:
                        "This course systematically unfolds crucial issues in corporate finance management at graduate level. We review the introductory aspect of the course, and build from there toward approaches of investment appraisals, explaining details as regards the methods like compound interest, net present value decision rule and the internal rate of return. Additionally, methods such as the annuities and perpetuities, sinking fund and discounted cash flows and tax payments would be fully discussed. <br/><br/> Issues in capital rationing and risk analysis are discussed under certain market conditions, considering the constraint, sensitivity analysis. <br/><br/> Our study efforts will also consider portfolio theory and capital asset pricing model, which is an important aspect of corporate financial management, together with the capital budgeting model and the associated specific tools used in portfolio theory and pricing decision. <br/><br/> Other topics such as market share value and pricing would be examined to give students advanced knowledge of the company‘s worth. We further go on to dividend decision, which is the crux of cash management concern, as against retained earning for futures operations and the rule of safety thumps",
                    link: "https://youtu.be/wRYf2ZcaeJY?feature=shared",
                    credit: 6,
                },
                {
                    name: "MBA 404. Accounting for Managers",
                    description:
                        "This course is designed to provide students with an understanding of the interplay between politics, economics, and geography in shaping international relations. Students will learn about the key concepts and theories of geopolitics and geoeconomics, as well as their practical applications in contemporary world affairs",
                    link: "https://youtu.be/daYqOgjHw94?feature=shared",
                    credit: 6,
                },
                {
                    name: "MBA 405. Digital Transformation",
                    description:
                        "The course will deepen the student’s comprehension of the digital transformation that is pervading society. The course will examine the process that contributes to the transitions to a digital society and economy. Therefore, digitalization is having an impact on different levels, affecting structures and the strategies of the organization, their ability to respond to external stimuli, the interactions with the stakeholders and the definition of the value proposition. Hence, the Digital transformation course aims also to support the students to understand the problems that the digital transformation might arise and, at the same time, to assess the new opportunities that the digitalization produces",
                    link: "",
                    credit: 6,
                },
                {
                    name: "MBA 406. Strategy Management",
                    description:
                        "This course introduces the key concepts, tools, and principles of strategy formulation and competitive analysis. It is concerned with managerial decisions and actions that affect the performance and survival of business enterprises. The course is focused on the information, analyses, organizational processes, and skills and business judgment managers must use to devise strategies, position their businesses, define firm boundaries and maximize long-term profits in the face of uncertainty and competition.  The course takes a general management perspective, viewing the firm as a whole, and examining how policies in each functional area are integrated into an overall competitive strategy. The key strategic business decisions of concern in this course involve choosing competitive strategies, creating competitive advantages, taking advantage of external opportunities, securing and defending sustainable market positions, and allocating critical resources over long periods. Decisions such as these can only be made effectively by viewing a firm holistically, and over the long term",
                    link: "",
                    credit: 6,
                },
                {
                    name: "MBA 407. Project Management",
                    description:
                        "Project management is a key contributor to organizational success and business results. This course teaches the fundamentals of project management. Students will learn how to select projects (portfolio management), lead and manage projects, build project teams, handle project risks, manage costs and schedules, control project progress, terminate projects, and integrate sustainability considerations throughout the project life cycle",
                    link: "",
                    credit: 6,
                },
                {
                    name: "MBA 408. Organizational Behaviour",
                    description:
                        "This course provides a comprehensive understanding of supply chain management, covering the key concepts, strategies, and practices involved in managing the flow of goods and services from suppliers to end customers. Students will explore various aspects of supply chain management, including procurement, logistics, inventory management, demand forecasting, and supplier relationships. The course aims to develop students' skills in designing, optimizing, and coordinating supply chain activities to achieve competitive advantage and operational excellence",
                    link: "",
                    credit: 6,
                },
                {
                    name: "MBA 409. Leadership Development",
                    description:
                        "This course approaches the Cross-Cultural Leadership from an organisational perspective, where individuals from different cultures, religions, and educational backgrounds interact on a daily basis. The organisation, where decisions are made collectively, promote people interactions and responsibilities incorporating the sense of importance of multiculturalism into its strategic vision. <br/><br/>  This course aims to develop an easily applied and solid rule to identify cross-cultural differences within the organisation that can create robust boundaries and link them to the effective attainment of group goals. At the same time, the course observes real-life cases where meaningful differences exist but address appropriately through leadership of cultural diversity",
                    link: "https://youtu.be/KK9lDNHYGo4",
                    credit: 6,
                },
                {
                    name: "MBA 500. Thesis Methodology",
                    description:
                        "This course aims to build the student’s critical ability to independently undertake and write a thesis which is comprehensive independent work. It explains how to choose a subject, to gather the documentation, to write the plan of work and the plan of writing, how to read books that student will use, how to quote, to write footnotes, the table of content, the bibliography and, in the end, how to prepare the defense. It also helps the student to examine written reports and investigations within the field of social science, from a critical and scientific point of view. ",
                    link: "",
                    credit: 6,
                },
                {
                    name: "MBA 501. Business Negotiation/AI in Business Decision Making",
                    description:
                        "Standard: AI in Business Decision Making  <br/> High Quality: Business Negotiation",
                    link: "",
                    credit: 6,
                },
                {
                    name: "MBA 505. MBA Thesis",
                    description:
                        "Complete an MBA thesis based on practical research and application of the knowledge learned",
                    link: "",
                    credit: 18,
                },
            ],
        },
        IDEAS03: {
            benefits: [
                "Các lớp học tương tác trực tuyến với giảng viên nước ngoài. Nâng cao khả năng, tự tin giao tiếp, trao đổi với Giáo sư và giải quyết các vấn đề thực tiễn trong doanh nghiệp.",
                "Công nghệ chatbox I-AI hỗ trợ các nội dung phù hợp trong chương trình MBA online do IDEAS quản lý. Trợ lý chương trình hỗ trợ nhắc nhở deadline bài tập, các vấn đề liên quan đến hệ thống, kết nối.",
                "IDEAS hỗ trợ: hệ thống IDEAS – LMS & lớp chuyên đề bổ trợ vào các ngày Chủ nhật, có hướng dẫn bài tập và đánh giá sơ bộ bài Final exam.",
                "Lễ tốt nghiệp và chuyến đi học tập tại trụ sở chính của trường – Geneva Thụy Sĩ.",
            ],
            program_name_degree: "Executive Master of Business Administration",
            program_benefits_degree: [
                "Tấm bằng Thạc sĩ danh giá được trao từ một trường đại học lâu đời và danh tiếng.",
                "Là cựu học viên của trường Swiss UMEF, tân Thạc Sĩ đánh dấu cột mốc quan trọng trên con đường phát triển.",
                "Chính thức trở thành chuyên gia – phát triển các kỹ năng lãnh đạo cần thiết để thúc đẩy sự thay đổi tích cực trong lĩnh vực của bạn.",
            ],
            link_iframe:
                "https://www.youtube.com/embed/ZrLeuFGGXQI?si=0tiJvbnRDzwEyo3B",
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
            subjects: "<b>60</b> tín chỉ ECTS - <b>10</b> môn không luận văn",
            duration: "14-16 tháng",
            country: "Thuỵ Sĩ",
            experience: [
                "Tốt nghiệp cử nhân",
                "Tối thiểu 2 năm kinh nghiệm làm việc",
                "Tiếng Anh giao tiếp tốt hoặc bằng cấp tương đương IELTS 6.0",
            ],
            test: {
                high: [
                    "Lớp học tương tác trực tiếp, được trao đổi và thảo luận cùng Giảng viên và học viên",
                    "Kiến thức hướng đến thực hành giải quyết các vấn đề quản lý chuyên sâu",
                    "Lộ trình học tinh gọn 10 môn - không làm luận văn",
                    "Học phí linh hoạt chia nhỏ hoặc trả góp",
                ],
                stand: [
                    "Thời gian học tập, nghiên cứu tự sắp xếp linh hoạt, hỗ trợ bởi các giảng viên Việt Nam trong quá trình học",
                    "Kiến thức hướng đến thực hành giải quyết các vấn đề quản lý chuyên sâu",
                    "Lộ trình học tinh gọn 10 môn - không làm luận văn",
                    "Học phí linh hoạt chia nhỏ hoặc trả góp",
                ],
            },
            highlight: [
                "Trực tuyến 100%",
                "Thạc sĩ (Master)",
                "20:00 - 23:00 (Vietnam)",
                "2 buổi chuyên đề/môn (Chủ nhật)",
            ],
            tagline:
                "Swiss UMEF: Đại học tư thục đầu tiên tại Geneva đạt Kiểm định Liên bang cao nhất Thụy Sĩ (SAC) - công nhận chính thức bởi Hội đồng Giáo dục Thụy Sĩ",
            link: "/emba",
            description:
                "Chương trình EMBA Online phù hợp cho người bận rộn. Bằng cấp được công nhận bởi Hội đồng Kiểm định Liên bang Thụy Sĩ, đảm bảo giá trị quốc tế, mang đến kiến thức thực tiễn và cập nhật theo xu hướng kinh doanh toàn cầu.",
            demographic: {
                jobs: [
                    {
                        jobname: "Quản trị kinh doanh",
                        percent: 30,
                    },
                    {
                        jobname: "Ngân hàng",
                        percent: 22,
                    },
                    {
                        jobname: "Công nghệ thông tin",
                        percent: 20,
                    },
                    {
                        jobname: "Start-up",
                        percent: 16,
                    },
                    {
                        jobname: "Khác",
                        percent: 12,
                    },
                ],
                ages: [
                    {
                        jobname: "18 - 24",
                        percent: 7,
                    },
                    {
                        jobname: "25 - 30",
                        percent: 27,
                    },
                    {
                        jobname: "31 - 40",
                        percent: 55,
                    },
                    {
                        jobname: "41 - 50",
                        percent: 10,
                    },
                    {
                        jobname: "51+",
                        percent: 1,
                    },
                ],
            },
            degree: {
                back: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0002.webp",
                front:
                    "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0001.webp",
                transcript:
                    "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0003.webp",
            },
            fee_plane: "3,000",
            fee_course: [
                // {
                //   name: "Standard",
                //   icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon6.png",
                //   price: "2.900 CHF",
                //   benefits: [
                //     "<b>Dành cho học viên đăng ký từ 01/08/2025:</b> Trợ phí từ trường giảm <b>500 CHF</b>.",
                //     "Tài khoản truy cập tài liệu học tập, môn học theo chương trình gốc của trường.",
                //     "Trợ lý chương trình nhắc deadline, hỗ trợ hệ thống, kết nối giảng viên qua Group Zalo/Email.",
                //     "I-AI: Chatbot hỗ trợ nội dung và giải đáp thắc mắc (IDEAS).",
                //     "Lớp chuyên đề của IDEAS tổ chức 2 ngày Chủ nhật vào tuần thứ 2 và thứ 4 mỗi môn, từ 8h30 - 11h30 và 13h30 - 16h30, nhằm giúp kiểm tra và hướng dẫn hoàn thành bài Final Exam.",
                //   ],
                // },
                {
                    name: "High Quality",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon5.png",
                    price: "8.900 CHF",
                    // price_promo: "4.165 CHF",
                    benefits: [
                        "Hỗ trợ trả góp 12 - 24 tháng qua Sacombank",
                        "Bao gồm chương trình Standard và Hệ thống học tập eAcademy.",
                        "Ứng dụng công nghệ - Platform Ai cho học tập do IDEAS phát triển",
                        "Lớp học GVNN vào các buổi tối trong tuần (tùy chọn)",
                        "Lịch học: mỗi môn học kéo dài 04 tuần, mỗi tuần có 02 buổi tối học với giảng viên nước ngoài của trường (lịch dự kiến rơi vào tối thứ 3 và tối thứ 5, mỗi buổi từ 2-3 tiếng, từ 20h00 - 22h00)",
                        // "Bao gồm 2 chuyến đi Thụy sĩ ( 03 ngày lưu trú, phí học tập/LTN, di chuyển nội bộ  Thụy sĩ).",
                        "Đánh giá sơ bộ bài Final: Được hội đồng chuyên môn của IDEAS đánh giá, góp ý bài Final đã đi đúng hướng, tránh lạc đề, học viên nắm được điểm đánh giá sơ bộ và hạn chế được khả năng rớt môn.",
                    ],
                },
            ],
            accreditation: [
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef1.png",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef3.png",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef4.png",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef5.png",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef2.png",
                    link: "#",
                },
            ],
            require: [
                "Bằng cử nhân và bảng điểm",
                "Sơ yếu lí lịch (CV)",
                "Thư động lực (Motivation Letter)",
                "Ảnh hộ chiếu (4×6)",
                "Passport",
                "Tiếng anh tương đương IELTS 6.0 hoặc có thể phỏng vấn đầu vào với đại diện của trường tại Việt Nam",
                "Admission Form <a href='https://ideas.edu.vn/wp-content/uploads/2025/03/ADMISSION-FORM_ENG_EMBA-_UMEF.docx' target='_blank' class='text_download'>(Download Here)</a>",
            ],
            faq: [
                {
                    q: "Chương trình EMBA có yêu cầu bảo vệ luận văn không?",
                    a: "Không. Chương trình EMBA của Swiss UMEF bao gồm 10 môn học và không yêu cầu bảo vệ luận văn. Học viên hoàn thành tất cả các môn học theo quy định sẽ đủ điều kiện nhận bằng EMBA do Swiss UMEF cấp. Điều này giúp học viên tối ưu thời gian học tập và tập trung vào việc nâng cao kỹ năng quản lý thực tiễn.",
                },
                {
                    q: "Lễ tốt nghiệp sau khi hoàn thành EMBA sẽ được tổ chức ở đâu, có thể qua Trường không?",
                    a: "Sau khi hoàn thành chương trình EMBA, học viên sẽ được tham gia lễ tốt nghiệp do Swiss UMEF tổ chức. Thông thường, lễ tốt nghiệp sẽ diễn ra tại Geneva, Thụy Sĩ, nơi đặt trụ sở chính của Trường. Tuy nhiên, Swiss UMEF cũng có thể tổ chức buổi lễ tốt nghiệp tại Việt Nam hoặc các địa điểm khác tùy theo số lượng học viên và điều kiện tổ chức. Học viên có thể lựa chọn tham dự lễ tốt nghiệp tại Geneva hoặc tại địa điểm do IDEAS phối hợp tổ chức.",
                },
                {
                    q: "Sau khi hoàn thành chương trình EMBA, có thể học lên Tiến sĩ không?",
                    a: "Có, sau khi hoàn thành chương trình EMBA của Swiss UMEF, học viên có thể tiếp tục học lên chương trình Tiến sĩ Quản trị Kinh doanh (DBA – Doctor of Business Administration) tại các trường đại học hoặc tổ chức giáo dục phù hợp. <br/> Tuy nhiên, việc được chấp nhận vào chương trình Tiến sĩ sẽ tùy thuộc vào đơn vị tiếp nhận. Một số trường có thể yêu cầu học viên hoàn thành thêm một số môn bổ sung để đáp ứng điều kiện đầu vào của chương trình DBA hoặc PhD. Học viên có thể liên hệ với IDEAS để được tư vấn cụ thể về lộ trình học tiếp và các yêu cầu xét tuyển.",
                },
            ],
            this_subjects: [
                {
                    name: "EMBA 400. Marketing Management",
                    description:
                        "This course progressively builds on marketing issues by exploring more advanced topics and strategic planning in the marketing concept. Our attention will be drawn to laying the groundwork by strategically planning all the marketing approaches and analyzing the external and internal environment of the organization. <br/><br/> This course also examines the dynamic environment inhabited by today's marketers, helping students understand the marketplace and integrate the appropriate information into marketing decisions. The course focuses on an integrated approach and strategy-based actions, covering only those critical and advanced issues required to succeed in future professional work. <br/><br/> Additionally, our focus includes the concept of marketing, the marketing manager's job, the development of a marketing strategy, marketing research, consumer behavior and analysis, organizational buying behavior; market structure and competitor analysis; marketing mix decision-making; communications and advertising strategy; channels of distribution; the personal sales channel; pricing; sales promotion; strategies for service markets; strategies for technology-based markets; global marketing strategies; and new product development",
                    link: "",
                    credit: 6,
                },
                {
                    name: " EMBA 401. Human Capital and Talent Management",
                    description:
                        "This course is designed to provide a fresh approach to management students, based on dynamic, real-life organizational events confronting both human resource managers and line managers who often implement personnel programs and policies. <br/><br/> Human Resource Management takes a managerial orientation and is viewed as being relevant to managers in every unit, project or team. Managers are constantly faced with human relation management issues, problems and decision making. <br/><br/>  This course will introduce management students to HR activities (recruitment and selection, training and development, reward management and performance appraisal) with the emphasis that it is the bundles of HR interventions and not single practices that develop effective human resources. Based on realistic, current trends confronting managers, this course will also expose the management students to contemporary issues, such as, employee engagement and organizational change, and how HR can work together with knowledge management, technology, internationalization - and talent management - to further develop the effectiveness of human resources and help create, develop and sustain a high-performing organization",
                    link: "https://youtu.be/CebD5PCML6w?si=qbXiR8r9eztjvUvM",
                    credit: 6,
                },
                {
                    name: "EMBA 402. Entrepreneurship and Innovation",
                    description:
                        "This course explores the principles and practices of entrepreneurship and innovation. Students will learn how to identify and evaluate entrepreneurial opportunities, develop innovative ideas, and create viable business models. The course emphasizes critical thinking, creativity, and problem-solving skills necessary for successful entrepreneurship. Students will also gain an understanding of the challenges and strategies involved in launching and growing a new venture in a dynamic business environment",
                    link: "https://youtu.be/t1g7aCRoC-I?si=5luqjqunPivUr5wL",
                    credit: 6,
                },
                {
                    name: "EMBA 403. Corporate Finance",
                    description:
                        "This course systematically unfolds crucial issues in corporate finance management at graduate level. We review the introductory aspect of the course, and build from there toward approaches of investment appraisals, explaining details as regards the methods like compound interest, net present value decision rule and the internal rate of return. Additionally, methods such as the annuities and perpetuities, sinking fund and discounted cash flows and tax payments would be fully discussed. <br/><br/> Issues in capital rationing and risk analysis are discussed under certain market conditions, considering the constraint, sensitivity analysis. <br/><br/> Our study efforts will also consider portfolio theory and capital asset pricing model, which is an important aspect of corporate financial management, together with the capital budgeting model and the associated specific tools used in portfolio theory and pricing decision. <br/><br/> Other topics such as market share value and pricing would be examined to give students advanced knowledge of the company‘s worth. We further go on to dividend decision, which is the crux of cash management concern, as against retained earning for futures operations and the rule of safety thumps",
                    link: "https://youtu.be/zk1-2CERWHs?si=tT-pLAUoA5iI_b3h",
                    credit: 6,
                },
                {
                    name: "EMBA 404. Accounting for Managers",
                    description:
                        "This course is designed to provide students with an understanding of the interplay between politics, economics, and geography in shaping international relations. Students will learn about the key concepts and theories of geopolitics and geoeconomics, as well as their practical applications in contemporary world affairs",
                    link: "",
                    credit: 6,
                },
                {
                    name: "EMBA 405. Digital Transformation",
                    description:
                        "The course will deepen the student’s comprehension of the digital transformation that is pervading society. The course will examine the process that contributes to the transitions to a digital society and economy. Therefore, digitalization is having an impact on different levels, affecting structures and the strategies of the organization, their ability to respond to external stimuli, the interactions with the stakeholders and the definition of the value proposition. Hence, the Digital transformation course aims also to support the students to understand the problems that the digital transformation might arise and, at the same time, to assess the new opportunities that the digitalization produces",
                    link: "",
                    credit: 6,
                },
                {
                    name: "EMBA 406. Strategy Management",
                    description:
                        "This course introduces the key concepts, tools, and principles of strategy formulation and competitive analysis. It is concerned with managerial decisions and actions that affect the performance and survival of business enterprises. The course is focused on the information, analyses, organizational processes, and skills and business judgment managers must use to devise strategies, position their businesses, define firm boundaries and maximize long-term profits in the face of uncertainty and competition.  The course takes a general management perspective, viewing the firm as a whole, and examining how policies in each functional area are integrated into an overall competitive strategy. The key strategic business decisions of concern in this course involve choosing competitive strategies, creating competitive advantages, taking advantage of external opportunities, securing and defending sustainable market positions, and allocating critical resources over long periods. Decisions such as these can only be made effectively by viewing a firm holistically, and over the long term",
                    link: "https://youtu.be/wVY3uLMG-Fk?si=rTdwcjAk6-tEgbqS",
                    credit: 6,
                },
                {
                    name: "EMBA 407. Project Management",
                    description:
                        "Project management is a key contributor to organizational success and business results. This course teaches the fundamentals of project management. Students will learn how to select projects (portfolio management), lead and manage projects, build project teams, handle project risks, manage costs and schedules, control project progress, terminate projects, and integrate sustainability considerations throughout the project life cycle",
                    link: "https://youtu.be/MMYVUtpiAPk?si=R1XbXLyILON4-yK6",
                    credit: 6,
                },
                {
                    name: "EMBA 408. Organizational Behaviour",
                    description:
                        "This course provides a comprehensive understanding of supply chain management, covering the key concepts, strategies, and practices involved in managing the flow of goods and services from suppliers to end customers. Students will explore various aspects of supply chain management, including procurement, logistics, inventory management, demand forecasting, and supplier relationships. The course aims to develop students' skills in designing, optimizing, and coordinating supply chain activities to achieve competitive advantage and operational excellence",
                    link: "",
                    credit: 6,
                },
                {
                    name: "EMBA 409. Leadership Development",
                    description:
                        "This course approaches the Cross-Cultural Leadership from an organisational perspective, where individuals from different cultures, religions, and educational backgrounds interact on a daily basis. The organisation, where decisions are made collectively, promote people interactions and responsibilities incorporating the sense of importance of multiculturalism into its strategic vision. <br/><br/>  This course aims to develop an easily applied and solid rule to identify cross-cultural differences within the organisation that can create robust boundaries and link them to the effective attainment of group goals. At the same time, the course observes real-life cases where meaningful differences exist but address appropriately through leadership of cultural diversity",
                    link: "",
                    credit: 6,
                },
            ],
        },
        IDEAS04: {
            benefits: [
                "Chương trình cung cấp kiến thức AI nền tảng và ứng dụng thực tiễn, giúp lãnh đạo doanh nghiệp triển khai AI để tối ưu vận hành, ra quyết định và phát triển bền vững.",
                "Hiểu biết sâu sắc về các công nghệ AI hiện đại. Ứng dụng AI vào quản lý, phân tích dữ liệu và ra quyết định.",
                "Phát triển kỹ năng lãnh đạo trong môi trường công nghệ cao.",
                "IDEAS hỗ trợ: hệ thống IDEAS – LMS & lớp chuyên đề bổ trợ vào các ngày Chủ nhật, có hướng dẫn bài tập và đánh giá sơ bộ bài Final exam.",
                "Lễ tốt nghiệp và chuyến đi học tập tại trụ sở chính của trường – Geneva Thụy Sĩ.",
            ],
            program_name_degree:
                "Master of Science in Applied Artificial Intelligence",
            program_benefits_degree: [
                "Tấm bằng Chuyên gia quản trị AI – MSc AI danh giá được trao từ một trường kinh doanh lâu đời tại Thuỵ Sĩ.",
                "Là cựu học viên của trường Swiss UMEF đánh dấu cột mốc quan trọng trên con đường phát triển.",
                "Chính thức trở thành chuyên gia quản trị bằng AI – phát triển các kỹ năng lãnh đạo, sử dụng các công cụ AI cần thiết để thúc đẩy sự thay đổi tích cực trong lĩnh vực của bạn.",
            ],
            link_iframe:
                "https://www.youtube.com/embed/mB0mDrgjVNs?si=wP6X9bDGqVVR2R28",
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
            subjects: "<b>90</b> tín chỉ ECTS - <b>12</b> môn và luận văn",
            duration: "18 tháng",
            country: "Thuỵ Sĩ",
            test: {
                high: [
                    "Vận dụng AI trong quản lý doanh nghiệp",
                    "Hiểu biết chuyên sâu hơn về cách thức quản trị của các doanh nghiệp bằng AI trong thời đại mới",
                    "Lớp học tương tác trực tiếp, được trao đổi và thảo luận cùng Giảng viên và học viên",
                    "Học phí linh hoạt chia nhỏ hoặc trả góp",
                ],
            },
            experience: [
                "Tốt nghiệp cử nhân",
                "Tối thiểu 2 năm kinh nghiệm ở cấp độ quản lý",
                "Tiếng Anh giao tiếp tốt hoặc bằng cấp tương đương IELTS 6.0",
            ],
            highlight: [
                "Trực tuyến 100%",
                "Thạc sĩ (Master)",
                "20:00 T3 - T5 (2-3h/buổi)",
                "2 buổi chuyên đề/môn (Chủ nhật)",
            ],
            tagline:
                "Swiss UMEF: Đại học tư thục đầu tiên tại Geneva đạt Kiểm định Liên bang cao nhất Thụy Sĩ (SAC) - công nhận chính thức bởi Hội đồng Giáo dục Thụy Sĩ",
            link: "/mscai",
            description:
                "Chương trình AI được thiết kế đặc biệt cho những nhà quản trị kinh doanh, những người muốn khám phá và khai thác tiềm năng của trí tuệ nhân tạo trong quản lý doanh nghiệp",
            demographic: {
                jobs: [
                    {
                        jobname: "Công nghệ thông tin",
                        percent: 40,
                    },
                    {
                        jobname: "Quản trị kinh doanh",
                        percent: 30,
                    },
                    {
                        jobname: "Ngân hàng",
                        percent: 12,
                    },

                    {
                        jobname: "Start-up",
                        percent: 10,
                    },
                    {
                        jobname: "Khác",
                        percent: 8,
                    },
                ],
                ages: [
                    {
                        jobname: "18 - 24",
                        percent: 7,
                    },
                    {
                        jobname: "25 - 30",
                        percent: 27,
                    },
                    {
                        jobname: "31 - 40",
                        percent: 55,
                    },
                    {
                        jobname: "41 - 50",
                        percent: 10,
                    },
                    {
                        jobname: "51+",
                        percent: 1,
                    },
                ],
            },

            degree: {
                back: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0002.webp",
                front:
                    "https://ideas.edu.vn/wp-content/uploads/2025/03/UMEF-MSc-Degree.jpg",
                transcript:
                    "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0003.webp",
            },
            fee_plane: "4,400",
            fee_course: [
                {
                    name: "High Quality",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon5.png",
                    price: "11.900 CHF",
                    price_promo: "4.165 CHF",
                    benefits: [
                        "Hỗ trợ trả góp 12 - 24 tháng qua Sacombank",
                        "Lớp học GVNN vào các buổi tối trong tuần (tùy chọn)",
                        "Ứng dụng công nghệ - Platform Ai cho học tập do IDEAS phát triển",
                        "Tài khoản truy cập tài liệu học tập, môn học theo chương trình gốc của trường",
                        "Trợ lý chương trình nhắc deadline, hỗ trợ hệ thống, kết nối giảng viên qua Group Zalo/Email.",
                        "Lịch học: mỗi môn học kéo dài 04 tuần, mỗi tuần có 02 buổi tối học với giảng viên nước ngoài của trường (lịch dự kiến rơi vào tối thứ 3 và tối thứ 5, mỗi buổi từ 2-3 tiếng, từ 20h00 - 22h00)",
                        // "Học phí bao gồm 2 chuyến đi tốt nghiệp tại Thuỵ Sĩ trị giá <b.>4,400 CHF</b.",
                        "Đánh giá sơ bộ bài Final: Được hội đồng chuyên môn của IDEAS đánh giá, góp ý bài Final đã đi đúng hướng, tránh lạc đề, học viên nắm được điểm đánh giá sơ bộ và hạn chế được khả năng rớt môn.",
                    ],
                },
            ],
            accreditation: [
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef1.png",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef3.png",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef4.png",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef5.png",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef2.png",
                    link: "#",
                },
            ],
            require: [
                "Bằng cử nhân và bảng điểm",
                "Sơ yếu lí lịch (CV)",
                "Thư động lực (Motivation Letter)",
                "Ảnh hộ chiếu (4×6)",
                "Passport",
                "Tiếng anh tương đương IELTS 6.0 hoặc có thể phỏng vấn đầu vào với đại diện của trường tại Việt Nam",
                "Admission Form <a href='https://ideas.edu.vn/wp-content/uploads/2025/03/ADMISSION-FORM_ENG_MSC_UMEF.docx' target='_blank' class='text_download'>(Download Here)</a>",
            ],
            faq: [
                {
                    q: "Không có bằng cử nhân về công nghệ hoặc khoa học máy tính, tôi có thể đăng ký học MSc AI không?",
                    a: "Có! Bạn hoàn toàn có thể đăng ký học MSc AI ngay cả khi không có bằng cử nhân về công nghệ hoặc khoa học máy tính. Chương trình MSc AI của Swiss UMEF được thiết kế theo hướng ứng dụng, không chỉ dành cho những ai có nền tảng công nghệ mà còn phù hợp với các chuyên gia, nhà quản lý, hoặc những người muốn ứng dụng AI vào lĩnh vực kinh doanh, tài chính, marketing, quản lý… Nếu bạn chưa có nền tảng về lập trình hoặc dữ liệu, chương trình sẽ có những học phần giúp bạn làm quen với các công cụ AI mà không cần kiến thức quá chuyên sâu về kỹ thuật. Bạn sẽ được hướng dẫn từng bước để áp dụng AI vào công việc thực tế.",
                },
                {
                    q: "Chương trình MSc AI có yêu cầu học viên phải có nền tảng hoặc sử dụng những ứng dụng đặc biệt nào không?",
                    a: "Chương trình MSc AI của Swiss UMEF được thiết kế theo hướng ứng dụng, phù hợp với học viên từ nhiều lĩnh vực khác nhau, kể cả những người chưa có nền tảng công nghệ. Để đảm bảo học viên có sự chuẩn bị tốt nhất, IDEAS hỗ trợ hai môn nền tảng: Machine Learning & Deep Learning – giúp học viên làm quen với các khái niệm cốt lõi trong AI. Quản trị AI – giúp học viên hiểu cách ứng dụng AI vào thực tế doanh nghiệp và quản lý công nghệ hiệu quả. Nhờ đó, dù bạn xuất phát từ lĩnh vực nào, bạn vẫn có thể theo học và áp dụng AI vào công việc của mình một cách vững vàng.",
                },
                {
                    q: "Có thể vừa học vừa đi làm không? Cường độ học có quá nặng không?",
                    a: "Hoàn toàn có thể! Chương trình MSc AI của Swiss UMEF được thiết kế linh hoạt, phù hợp với người đang đi làm. Học trực tuyến, giúp bạn chủ động sắp xếp thời gian, nhưng cần đảm bảo tham gia lớp học vì chương trình yêu cầu không được vắng quá 30% số giờ học. Thời gian vào buổi tối rất thuận tiện sắp xếp theo khung giờ của Việt Nam. IDEAS hỗ trợ học viên thêm thông qua các lớp chuyên đề, chia sẻ bài tập và tổ chức workshop/seminar giúp học viên hiểu sâu hơn về ứng dụng thực tế của AI.",
                },
            ],
            this_subjects: [
                {
                    name: "Artificial Intelligence and Machine Learning",
                    description:
                        "Môn học giới thiệu các khái niệm cơ bản về trí tuệ nhân tạo (AI) và học máy (ML), bao gồm học có giám sát, không giám sát, mạng nơ-ron nhân tạo và các mô hình học sâu (Deep Learning). Sinh viên sẽ áp dụng các kỹ thuật này vào dữ liệu thực tế trong nhiều ngành nghề khác nhau.",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Digital and Computer Vision",
                    description:
                        "Môn học trang bị kiến thức về xử lý ảnh số và thị giác máy tính, bao gồm các kỹ thuật như trích xuất đặc trưng, nhận dạng đối tượng, phát hiện chuyển động, và các ứng dụng thực tiễn trong robot, xe tự hành và AI.",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Big Data Analytics",
                    description:
                        "Môn học tập trung vào việc thu thập, lưu trữ, xử lý và phân tích dữ liệu lớn. Sinh viên sẽ làm quen với các công cụ như Hadoop, Spark, và áp dụng phân tích dữ liệu để hỗ trợ ra quyết định trong kinh doanh và công nghệ.",
                    link: "",
                    credit: 6,
                },
                {
                    name: "IoT",
                    description:
                        "Môn học cung cấp kiến thức về thiết kế, điều khiển và vận hành hệ thống IoT trong công nghiệp, y tế và đời sống.",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Reinforcement Learning and AI Optimization",
                    description:
                        "Môn học đi sâu vào học tăng cường (Reinforcement Learning) và các kỹ thuật tối ưu hóa trong AI. Bao gồm các khái niệm như MDP, Q-learning, Deep Q-Network và ứng dụng trong robot, game, tài chính.",
                    link: "",
                    credit: 6,
                },
                {
                    name: "AI in Business Decision Making",
                    description:
                        "Môn học giới thiệu cách ứng dụng AI vào các quyết định kinh doanh như tài chính, marketing, vận hành và nhân sự. Sinh viên sẽ học cách dùng phân tích dự đoán, tự động hóa và AI để tối ưu hóa hiệu suất tổ chức.",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Economic Forecasting and AI-Driven Market Dynamics",
                    description:
                        "Môn học tập trung vào việc dự báo kinh tế và phân tích thị trường bằng AI. Sinh viên sẽ học cách dùng các mô hình học máy để phân tích dữ liệu kinh tế, hành vi thị trường và hỗ trợ ra quyết định tài chính.",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Financial Intelligence and Algorithmic Trading",
                    description:
                        "Môn học tập trung vào ứng dụng AI trong tài chính và giao dịch thuật toán. Sinh viên sẽ học cách xây dựng mô hình giao dịch tự động, tối ưu hóa danh mục đầu tư và phân tích rủi ro tài chính bằng AI.",
                    link: "",
                    credit: 6,
                },
                {
                    name: "AI Innovation and Entrepreneurship",
                    description:
                        "Môn học giúp sinh viên phát triển các ý tưởng kinh doanh và đổi mới sáng tạo dựa trên AI. Nội dung bao gồm phát hiện cơ hội thị trường, phát triển sản phẩm và xây dựng chiến lược khởi nghiệp với công nghệ AI.",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Advanced Project Management in AI",
                    description:
                        "Môn học cung cấp các kỹ năng quản lý dự án chuyên sâu trong lĩnh vực AI. Sinh viên sẽ học về quản lý thời gian, nguồn lực, rủi ro và phương pháp Agile/Scrum áp dụng trong dự án công nghệ AI.",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Change management strategies for AI transition",
                    description:
                        "Môn học trang bị chiến lược và kỹ năng quản lý thay đổi trong tổ chức khi triển khai công nghệ AI. Bao gồm kỹ năng lãnh đạo, giao tiếp, và xử lý kháng cự để đảm bảo quá trình chuyển đổi thành công.",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Global AI policies and regulatory frameworks",
                    description:
                        "Môn học nghiên cứu các chính sách và quy định về AI trên toàn cầu. Sinh viên sẽ phân tích tác động của luật pháp đến đổi mới công nghệ và những thách thức trong việc xây dựng khung pháp lý phù hợp cho AI.",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Capstone Project",
                    description:
                        "Đồ án tốt nghiệp toàn diện, nơi sinh viên vận dụng các kiến thức và kỹ năng đã học để thực hiện một dự án AI thực tế hoặc viết báo cáo thực tập chuyên sâu.",
                    link: "",
                    credit: 18,
                },
            ],
        },
        IDEAS05: {
            benefits: [
                "Chương trình kết hợp độc đáo giữa quản trị kinh doanh hiện đại và ứng dụng AI no-code tiên phong.",
                "Học trực tuyến linh hoạt 100%, giảng dạy bởi các giáo sư hàng đầu Thụy Sĩ và chuyên gia quốc tế.",
                "Được hỗ trợ học bổng 70% học phí cơ bản từ quỹ hợp tác giáo dục của Swiss UMEF và IDEAS.",
                "Mạng lưới học viên là các nhà quản lý, lãnh đạo cấp cao trong kỷ nguyên số."
            ],
            program_name_degree: "MBA in Artificial Intelligence",
            program_benefits_degree: [
                "Bằng Thạc sĩ Quản trị Kinh doanh chuyên sâu AI chuẩn Châu Âu từ Swiss UMEF Thụy Sĩ.",
                "Làm chủ tư duy lãnh đạo chiến lược và năng lực điều hành các dự án chuyển đổi số dựa trên AI.",
                "Không cần nền tảng kỹ thuật/code, tập trung vào ứng dụng thực tiễn của các mô hình AI."
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
                "Trực tuyến 100%",
                "MBA chuyên sâu AI",
                "16-18 tháng",
                "90 ECTS"
            ],
            school: "Swiss UMEF",
            country: "Thụy Sĩ",
            subjects: "<b>90</b> ECTS - <b>12</b> môn học và Luận văn tốt nghiệp",
            duration: "16-18 tháng",
            tagline: "Swiss UMEF: Đại học tư thục đầu tiên tại Geneva đạt Kiểm định Liên bang cao nhất Thụy Sĩ (SAC) - công nhận chính thức bởi Hội đồng Giáo dục Thụy Sĩ",
            link: "/mbainai",
            experience: [
                "Tốt nghiệp Đại học",
                "Tối thiểu 3 năm kinh nghiệm làm việc",
                "Tiếng Anh tương đương IELTS 6.0 hoặc đạt đánh giá phỏng vấn đầu vào"
            ],
            fee_course: [
                {
                    name: "Standard",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon6.png",
                    price: "4.520 CHF",
                    benefits: [
                        "Bao gồm phí xét tuyển 150 CHF.",
                        "Học phí Platform 3.870 CHF (đã giảm 70%).",
                        "Phí hỗ trợ học vụ cơ bản IDEAS 500 CHF."
                    ]
                },
                {
                    name: "High Quality",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon5.png",
                    price: "7.970 CHF",
                    benefits: [
                        "Bao gồm toàn bộ quyền lợi của gói Standard.",
                        "Lớp chuyên đề bổ trợ, Hướng dẫn luận văn 1:1 bởi chuyên gia IDEAS.",
                        "LMS IDEAS tích hợp trợ lý AI học tập.",
                        "Phí Canton tốt nghiệp, Lãnh sự Thụy Sĩ & Hợp pháp hóa bằng (300 CHF)."
                    ]
                }
            ],
            description: "Chương trình MBA in AI từ Swiss UMEF là sự kết hợp tối ưu giữa lý thuyết quản trị kinh doanh truyền thống và các công nghệ AI ứng dụng thực chiến trong điều hành doanh nghiệp.",
            degree: {
                front: "https://ideas.edu.vn/wp-content/uploads/2025/11/z7191978013846_36a81ec39301d05fedaf6d4cd0293f9c-1.webp",
                back: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0002.webp"
            },
            require: [
                "Bằng tốt nghiệp Đại học và bảng điểm",
                "Sơ yếu lý lịch (CV) chi tiết kinh nghiệm",
                "Thư động lực (Motivation Letter)",
                "Ảnh hộ chiếu (4x6)",
                "Passport còn hạn",
                "Application Form của Swiss UMEF"
            ],
            this_subjects: [
                { name: "Marketing Management", description: "Quản trị tiếp thị chiến lược trong kỷ nguyên số" },
                { name: "Human Capital and Talent Management", description: "Quản trị nguồn nhân lực và phát triển nhân tài" },
                { name: "Entrepreneurship and Innovation", description: "Tinh thần khởi nghiệp và thúc đẩy đổi mới sáng tạo" },
                { name: "Corporate Finance", description: "Tài chính doanh nghiệp và hoạch định nguồn vốn" },
                { name: "Accounting for Managers", description: "Kế toán dành cho nhà quản lý phục vụ điều hành" },
                { name: "Digital Transformation", description: "Chiến lược chuyển đổi số và quản lý thay đổi" },
                { name: "Global Strategy", description: "Chiến lược kinh doanh toàn cầu trong môi trường biến động" },
                { name: "Organizational Behaviour", description: "Hành vi tổ chức và tối ưu hóa hiệu suất đội ngũ" },
                { name: "Leadership Development", description: "Phát triển năng lực lãnh đạo chiến lược" },
                { name: "AI in Business Decision making", description: "Ra quyết định kinh doanh đột phá dựa trên dữ liệu & AI" },
                { name: "Big Data Analysis", description: "Phân tích dữ liệu lớn định hướng chiến lược" },
                { name: "Project Management", description: "Quản trị dự án chuyên nghiệp chuẩn quốc tế" },
                { name: "Thesis", description: "Luận văn tốt nghiệp thạc sĩ nghiên cứu ứng dụng AI vào doanh nghiệp", credit: 18 }
            ]
        },
        IDEAS06: {
            pay_rule: `
       <img src="https://ideas.edu.vn/wp-content/new_public/data_imgs/icon2.png"/>
        <p><b>Thanh toán một lần hoặc chia thành 4 lần</b></p>
      <ul>
        <li><b><i class="fa-solid fa-file-invoice-dollar"></i> Học viên thanh toán 1 lần được trợ phí 20% từ IDEAS</b></li>
        <li><i class="fa-solid fa-file-invoice-dollar"></i> <b>Hoặc thanh toán 4 lần chia thành các đợt: 40%, 20%, 20%, 20% </b></li>
        <li><br/></li>
        <li><b class="main_clr">Hình thức thanh toán</b></li>
        <li><i class="fa-solid fa-check"></i> Thanh toán trực tiếp tại IDEAS</li>
        <li><i class="fa-solid fa-check"></i> Chuyển khoản trực tuyến qua thông tin số tài khoản được cung cấp trong hợp đồng tư vấn hoặc qua đường link Payoo. </li>
        <li><i class="fa-solid fa-check"></i> IDEAS cung cấp phiếu thu hộ học phí (Khi thanh toán trực tiếp hoặc cà thẻ trực tiếp) hoặc email xác nhận đóng học phí mà học viên đã đóng sau khoảng 01 ngày làm việc. Trường hợp học viên đóng phí vào ngày nghỉ cuối tuần, nghỉ lễ, email xác nhận đóng phí sẽ được Viện thực hiện vào ngày làm việc liền kề sau ngày nghỉ. Khoản phí thu hộ không thể xuất hóa đơn VAT theo quy định của Nhà nước Việt Nam.</li>
        </ul>
      `,
            benefits: [
                "Mang lại lợi thế cạnh tranh vượt trội, vì học viên DBA không chỉ am hiểu sâu sắc trong lĩnh vực chuyên môn mà còn được trang bị các kỹ năng quản lý nâng cao dành cho cấp lãnh đạo cao nhất.",
                "Kích thích tư duy học thuật và phát triển trí tuệ, rèn luyện khả năng tư duy chiến lược và phân tích ở cấp độ cao.",
                "Khác biệt trong đám đông, thăng tiến trong sự nghiệp nắm bắt các xu hướng mới nhất, lý thuyết hiện đại và những vấn đề thời sự trong quản trị và kinh doanh.",
                "IDEAS hỗ trợ: hệ thống IDEAS – LMS & lớp chuyên đề bổ trợ vào các ngày Chủ nhật, có hướng dẫn bài tập và đánh giá sơ bộ bài Final exam.",
            ],
            program_name_degree: "Dual DBA",
            program_benefits_degree: [
                "Tấm bằng DBA, cấp độ cao nhất của học vị, từ một trường kinh doanh lớn, danh tiếng và campus trãi dài rộng khắp thế giới.",
                "Tân Tiến Sĩ của trường Ascencia Business School đánh dấu cột mốc cực kỳ quan trọng trên con đường phát triển.",
                "Là một chuyên gia thực thụ có đầy đủ các kỹ năng lãnh đạo cần thiết để thúc đẩy sự thay đổi lớn trong lĩnh vực của bạn.",
            ],
            link_iframe:
                "https://ideas.edu.vn/wp-content/uploads/2025/10/Dual-DBA.webp",
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
                "Trực tuyến 100%",
                "DBA",
                "Nghiên cứu với cố vấn",
                "2 buổi chuyên đề/môn (Chủ nhật)",
            ],
            school: "Estiam & RB College",
            country: "Anh - Pháp",

            subjects: "<b>4</b> giai đoạn | <b>2.5 - 3</b> năm - 1 luận văn",
            duration: "2.5 - 3 năm",
            tagline: "Nhận bằng DBA Anh và Pháp của Estiam và RB College",
            link: "/dual-dba-estiam-rb",
            experience: [
                "Bằng và bảng điểm Thạc sĩ hoặc tương đương.",
                "Có chứng chỉ IELTS 6.0/TOEFL 60 hoặc bằng chứng khác thể hiện năng lực tiếng Anh",
            ],
            // test: [
            //   "Chương trình học tập, làm bài và nộp trên flatform của trường, dựa trên tài liệu và hướng dẫn của Giáo sư",
            //   "Kiến thức quản trị chuyên sâu, kết hợp với case study thực tiễn",
            //   "Thời gian học tập tự sắp xếp linh hoạt",
            // ],
            fee_plane: "4,400",
            fee_course: [
                {
                    name: "High Quality",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon5.png",
                    price: "12.500 Euro",
                    benefits: [
                        "Trợ phí <b class='main_clr'>3,000 EURO</b> cho cựu học viên khi đăng ký chương trình",
                        "Hệ thống LMS hỗ trợ: ứng dụng I-AI để hỗ trợ trong quá trình học tập.",
                        "Thời gian nghiên cứu: 2.5 – 4 năm.",
                        "Trợ lý chương trình nhắc deadline, hỗ trợ hệ thống, kết nối giảng viên qua Group Zalo/Email.",
                        "Tự nghiên cứu với sự định hướng và hướng dẫn của Giáo sư.",

                        "Đánh giá sơ bộ bài Final: Được hội đồng chuyên môn của IDEAS đánh giá, góp ý bài Final đã đi đúng hướng, tránh lạc đề, học viên nắm được điểm đánh giá sơ bộ và hạn chế được khả năng rớt môn.",
                    ],
                },
            ],

            description:
                "Bằng DBA là bằng cấp tiến sĩ trong lĩnh vực quản trị kinh doanh. Chương trình này dành cho các chuyên gia giàu kinh nghiệm muốn nâng cao kiến ​​thức, kỹ năng và chuyên môn trong lĩnh vực lãnh đạo, nghiên cứu và quản lý kinh doanh.",
            demographic: {
                jobs: [
                    {
                        jobname: "Quản trị kinh doanh",
                        percent: 30,
                    },
                    {
                        jobname: "Ngân hàng",
                        percent: 22,
                    },
                    {
                        jobname: "Công nghệ thông tin",
                        percent: 20,
                    },
                    {
                        jobname: "Start-up",
                        percent: 16,
                    },
                    {
                        jobname: "Khác",
                        percent: 12,
                    },
                ],
                ages: [
                    {
                        jobname: "18 - 24",
                        percent: 7,
                    },
                    {
                        jobname: "25 - 30",
                        percent: 27,
                    },
                    {
                        jobname: "31 - 40",
                        percent: 55,
                    },
                    {
                        jobname: "41 - 50",
                        percent: 10,
                    },
                    {
                        jobname: "51+",
                        percent: 1,
                    },
                ],
            },
            degree: {
                front:
                    "https://ideas.edu.vn/wp-content/uploads/2025/10/Estiam-DBA-2.webp",
                back: "https://ideas.edu.vn/wp-content/uploads/2025/10/RB-DBA-2.webp",
            },
            accreditation: [
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/DUAL-DEGREE-2.webp",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/DUAL-DEGREE-1.webp",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/DUAL-DEGREE-3.webp",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/DUAL-DEGREE-5.webp",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/DUAL-DEGREE.webp",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/DUAL-DEGREE4.webp",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/uploads/2025/10/Erasmus_Logo.svg.webp",
                    link: "#",
                },
            ],
            require: [
                "Giấy tờ tùy thân: Bản scan trang đầu hộ chiếu, Ảnh hộ chiếu (4×6)",
                "Bằng và bảng điểm: Xác nhận đã có bằng Thạc sĩ hoặc tương đương.",
                "Trình độ tiếng Anh: Có chứng chỉ IELTS 6.0/TOEFL 60 hoặc bằng chứng khác thể hiện năng lực tiếng Anh.",
                "Thư bày tỏ nguyện vọng: Viết khoảng 250 từ, thể hiện mục tiêu và lý do chọn chương trình.",
                "CV chi tiết: Mô tả kinh nghiệm học tập và làm việc chuyên môn.",
                "Thư giới thiệu: Hai thư giới thiệu từ trường học hoặc cơ quan nơi ứng viên đã từng học tập, công tác.",
                "Application Form <a href='https://ideas.edu.vn/wp-content/uploads/2025/10/Application-Form-.docx' target='_blank' class='text_download'>(Download Here)</a>",
            ],
            faq: [
                {
                    q: "Chưa có kinh nghiệm nghiên cứu có thể theo học chương trình Dual DBA không?",
                    a: "Có. Chương trình Dual DBA được thiết kế dành cho các nhà quản lý, lãnh đạo muốn nâng cao tư duy chiến lược và năng lực nghiên cứu ứng dụng trong kinh doanh. IDEAS cung cấp hướng dẫn chi tiết về phương pháp nghiên cứu, giúp học viên từng bước xây dựng luận án DBA mà không cần nền tảng nghiên cứu trước đó.",
                },
                {
                    q: "Nếu bận đi công tác không thể hoàn thành bài đúng hạn thì sao?",
                    a: "Chương trình Dual DBA được thiết kế linh hoạt để phù hợp với lịch trình bận rộn của học viên. Mỗi môn học đều có thời gian nghiên cứu cùng với giáo viên cố vấn. Deadline các bài tập thường vào lúc buổi tối Việt Nam. Học viên nắm được thông tin bài tập và deadline sẽ sắp xếp phù hợp. Trường hợp Học viên chưa thể hoàn thành đúng thời hạn sẽ được đăng ký <b>Resubmit</b> có tốn phí để được mở thêm 1 tuần nộp lại bài. ",
                },
                {
                    q: "Học viên có thể tham gia lễ tốt nghiệp tại Paris - Pháp không?",
                    a: "Có. Học viên hoàn thành chương trình có thể đăng ký tham gia lễ tốt nghiệp tại Pháp, cùng với các học viên quốc tế khác của Ascencia Business School. Mỗi năm có 2 đợt Lễ Tốt nghiệp vào tháng 6 và tháng 12. IDEAS sẽ thông báo đến học viên về lịch thông báo từ Trường để học viên đăng kí.",
                },
            ],
            this_subjects: [
                {
                    name: "Giai đoạn 1: Lợi thế cạnh tranh",
                    description:
                        "<b>Khoảng 12 tháng</b> <br/> Tham dự 6 buổi seminar (mỗi buổi từ 2–3 giờ) cùng với các buổi hướng dẫn cá nhân 1:1. <br/> Soạn thảo đề cương luận án (thesis proposal) một cách toàn diện.",
                    link: "",
                },
                {
                    name: "Giai đoạn 2: Lựa chọn Giáo sư hướng dẫn",
                    description:
                        "<b>Khoảng 3 tháng</b> <br/>  Lựa chọn giảng viên hướng dẫn từ IDEAS hoặc Estiam (có phí). <br/> Hoàn thiện và chốt đề cương cùng kế hoạch nghiên cứu. <br/> Kết thúc giai đoạn: Đăng ký đề tài chính thức.",
                    link: "",
                },
                {
                    name: "Giai đoạn 3: Nghiên cứu cùng với sự hướng dẫn trực tiếp (1-1)",
                    description:
                        "<b>Khoảng 15 - 18 tháng</b> <br/> Nộp báo cáo tiến độ định kỳ. <br/> Hoàn thành toàn bộ luận án với độ dài khoảng 50.000 từ (200–250 trang).",
                    link: "",
                },
                {
                    name: "Giai đoạn 4: Bảo vệ luận văn tốt nghiệp",
                    description:
                        "<b>Khoảng 3 tháng</b> <br/> Hoàn thiện toàn bộ luận án và bảo vệ trước Hội đồng chấm luận văn.",
                    link: "",
                },
            ],
        },
        IDEAS07: {
            benefits: [
                "Các lớp học tương tác trực tuyến với giảng viên nước ngoài. Nâng cao khả năng, tự tin giao tiếp, trao đổi với Giáo sư và giải quyết các vấn đề thực tiễn trong doanh nghiệp.",
                "Công nghệ chatbox I-AI hỗ trợ các nội dung phù hợp trong chương trình MBA online do IDEAS quản lý. Trợ lý chương trình hỗ trợ nhắc nhở deadline bài tập, các vấn đề liên quan đến hệ thống, kết nối.",
                "IDEAS hỗ trợ: hệ thống IDEAS – LMS & lớp chuyên đề bổ trợ vào các ngày Chủ nhật, có hướng dẫn bài tập và đánh giá sơ bộ bài Final exam.",
                "Lễ tốt nghiệp và chuyến đi học tập tại trụ sở chính của trường – Geneva Thụy Sĩ.",
            ],
            program_name_degree: "Bachelor of Business Administration",
            program_benefits_degree: [
                "Tấm bằng Cử nhân danh giá được trao từ một trường đại học lâu đời và danh tiếng.",
                "Là cựu học viên của trường Swiss UMEF, tân Cử nhân đánh dấu cột mốc quan trọng trên con đường phát triển.",
                "Mở rộng con đường học lên Thạc sĩ - Tiến sĩ và phát triển sự nghiệp trong tương lai",
            ],
            link_iframe:
                "https://www.youtube.com/embed/ZrLeuFGGXQI?si=0tiJvbnRDzwEyo3B",
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
            subjects: "<b>60</b> tín chỉ ECTS - <b>10</b> môn và capstone",
            duration: "14-16 tháng",
            country: "Thuỵ Sĩ",
            experience: [
                "Tốt nghiệp cao đẳng/trung cấp - Đại học năm 2",
                "Tiếng Anh giao tiếp tốt hoặc bằng cấp tương đương IELTS 6.0",
            ],
            test: {
                high: [
                    "Lớp học tương tác trực tiếp, được trao đổi và thảo luận cùng Giảng viên và học viên",
                    "Kiến thức hướng đến thực hành giải quyết các vấn đề quản lý chuyên sâu",
                    "Lộ trình học tinh gọn 10 môn - không làm luận văn",
                    "Học phí linh hoạt chia nhỏ hoặc trả góp",
                ],
                stand: [
                    "Thời gian học tập, nghiên cứu tự sắp xếp linh hoạt, hỗ trợ bởi các giảng viên Việt Nam trong quá trình học",
                    "Kiến thức hướng đến thực hành giải quyết các vấn đề quản lý chuyên sâu",
                    "Lộ trình học tinh gọn 10 môn - không làm luận văn",
                    "Học phí linh hoạt chia nhỏ hoặc trả góp",
                ],
            },
            highlight: [
                "Trực tuyến 100%",
                "Liên thông lên Cử nhân",
                "20:00 - 23:00 (Vietnam)",
                "2 buổi chuyên đề/môn (Chủ nhật)",
            ],
            tagline:
                "Swiss UMEF: Đại học tư thục đầu tiên tại Geneva đạt Kiểm định Liên bang cao nhất Thụy Sĩ (SAC) - công nhận chính thức bởi Hội đồng Giáo dục Thụy Sĩ",
            link: "/bba",
            description:
                "Chương trình liên thông lên Cử nhân trong 1 năm đào tạo liên thông trực tuyến Cử nhân Quản trị Kinh doanh, đừng để tấm bằng Cử nhân là rào cản trước mọi con đường thăng tiến của bạn!",
            demographic: {
                jobs: [
                    {
                        jobname: "Quản trị kinh doanh",
                        percent: 30,
                    },
                    {
                        jobname: "Ngân hàng",
                        percent: 22,
                    },
                    {
                        jobname: "Công nghệ thông tin",
                        percent: 20,
                    },
                    {
                        jobname: "Start-up",
                        percent: 16,
                    },
                    {
                        jobname: "Khác",
                        percent: 12,
                    },
                ],
                ages: [
                    {
                        jobname: "18 - 24",
                        percent: 7,
                    },
                    {
                        jobname: "25 - 30",
                        percent: 27,
                    },
                    {
                        jobname: "31 - 40",
                        percent: 55,
                    },
                    {
                        jobname: "41 - 50",
                        percent: 10,
                    },
                    {
                        jobname: "51+",
                        percent: 1,
                    },
                ],
            },
            degree: {
                back: "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0002.webp",
                front: "https://ideas.edu.vn/wp-content/uploads/2026/06/bba-degree.webp",
                transcript:
                    "https://ideas.edu.vn/wp-content/uploads/2025/11/Sample_page-0003.webp",
            },
            // fee_plane: "3,000",
            fee_course: [
                // {
                //   name: "Standard",
                //   icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon6.png",
                //   price: "2.900 CHF",
                //   benefits: [
                //     "<b>Dành cho học viên đăng ký từ 01/08/2025:</b> Trợ phí từ trường giảm <b>500 CHF</b>.",
                //     "Tài khoản truy cập tài liệu học tập, môn học theo chương trình gốc của trường.",
                //     "Trợ lý chương trình nhắc deadline, hỗ trợ hệ thống, kết nối giảng viên qua Group Zalo/Email.",
                //     "I-AI: Chatbot hỗ trợ nội dung và giải đáp thắc mắc (IDEAS).",
                //     "Lớp chuyên đề của IDEAS tổ chức 2 ngày Chủ nhật vào tuần thứ 2 và thứ 4 mỗi môn, từ 8h30 - 11h30 và 13h30 - 16h30, nhằm giúp kiểm tra và hướng dẫn hoàn thành bài Final Exam.",
                //   ],
                // },
                {
                    name: "TOP-UP BBA",
                    icon: "https://ideas.edu.vn/wp-content/new_public/data_imgs/icon5.png",
                    price: "3.000 CHF",
                    // price_promo: "4.165 CHF",
                    benefits: [
                        "Hỗ trợ trả góp 12 tháng qua <b class='main_clr'>Sacombank</b>",
                        "Ứng dụng công nghệ - Platform Ai cho học tập do IDEAS phát triển",
                        "Lớp học GVNN vào các buổi tối trong tuần (tùy chọn)",
                        "Lịch học: mỗi môn học kéo dài 04 tuần, mỗi tuần có 02 buổi tối học với giảng viên nước ngoài của trường (lịch dự kiến rơi vào tối thứ 3 và tối thứ 5, mỗi buổi từ 2-3 tiếng, từ 20h00 - 22h00)",
                        // "Bao gồm 2 chuyến đi Thụy sĩ ( 03 ngày lưu trú, phí học tập/LTN, di chuyển nội bộ  Thụy sĩ).",
                        "Đánh giá sơ bộ bài Final: Được hội đồng chuyên môn của IDEAS đánh giá, góp ý bài Final đã đi đúng hướng, tránh lạc đề, học viên nắm được điểm đánh giá sơ bộ và hạn chế được khả năng rớt môn.",
                    ],
                },
            ],
            accreditation: [
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef1.png",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef3.png",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef4.png",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef5.png",
                    link: "#",
                },
                {
                    name: "Công nhận và Kiểm định",
                    logo: "https://ideas.edu.vn/wp-content/new_public/data_imgs/kdumef2.png",
                    link: "#",
                },
            ],
            require: [
                "Bằng cao đẳng hoặc bảng điểm đại học",
                "Sơ yếu lí lịch (CV)",
                "Thư động lực (Motivation Letter)",
                "Ảnh hộ chiếu (4×6)",
                "Passport",
                "Tiếng anh tương đương IELTS 6.0 hoặc có thể phỏng vấn đầu vào với đại diện của trường tại Việt Nam",
                "<b class='main_clr'>*</b>Tham khảo chương trình PRE-TOP UP nếu bạn chưa đủ điều kiện <a href='#' target='_blank' class='text_download'>PRE TOP-UP</a>",
            ],
            faq: [
                {
                    q: "Chương trình Liên thông có yêu cầu bảo vệ luận văn không?",
                    a: "Không. Chương trình liên thông Cao đẳng - Cử nhân của Swiss UMEF bao gồm 10 môn học và không yêu cầu bảo vệ luận văn chỉ cần làm Capstone Project tập trung vào việc nâng cao kỹ năng quản lý thực tiễn.",
                },
                {
                    q: "Lễ tốt nghiệp sau khi hoàn thành Top up sẽ được tổ chức ở đâu, có thể qua Trường không?",
                    a: "Sau khi hoàn thành chương trình Top up BBA, học viên sẽ được tham gia lễ tốt nghiệp do Swiss UMEF tổ chức. Thông thường, lễ tốt nghiệp sẽ diễn ra tại Geneva, Thụy Sĩ, nơi đặt trụ sở chính của Trường. Tuy nhiên, Swiss UMEF cũng có thể tổ chức buổi lễ tốt nghiệp tại Việt Nam hoặc các địa điểm khác tùy theo số lượng học viên và điều kiện tổ chức. Học viên có thể lựa chọn tham dự lễ tốt nghiệp tại Geneva hoặc tại địa điểm do IDEAS phối hợp tổ chức.",
                },
                {
                    q: "Sau khi hoàn thành chương trình Top up BBA, có thể học lên Thạc sĩ không?",
                    a: "Có, sau khi hoàn thành chương trình Top up BBA của Swiss UMEF, học viên có thể tiếp tục học lên chương trình Thạc sĩ Quản trị Kinh doanh (MBA – Master of Business Administration) tại các trường đại học hoặc tổ chức giáo dục phù hợp.",
                },
            ],
            this_subjects: [
                {
                    name: "Introduction to Management",
                    description:
                        "This course progressively builds on marketing issues by exploring more advanced topics and strategic planning in the marketing concept. Our attention will be drawn to laying the groundwork by strategically planning all the marketing approaches and analyzing the external and internal environment of the organization. <br/><br/> This course also examines the dynamic environment inhabited by today's marketers, helping students understand the marketplace and integrate the appropriate information into marketing decisions. The course focuses on an integrated approach and strategy-based actions, covering only those critical and advanced issues required to succeed in future professional work. <br/><br/> Additionally, our focus includes the concept of marketing, the marketing manager's job, the development of a marketing strategy, marketing research, consumer behavior and analysis, organizational buying behavior; market structure and competitor analysis; marketing mix decision-making; communications and advertising strategy; channels of distribution; the personal sales channel; pricing; sales promotion; strategies for service markets; strategies for technology-based markets; global marketing strategies; and new product development",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Introduction to Finance",
                    description:
                        "This course is designed to provide a fresh approach to management students, based on dynamic, real-life organizational events confronting both human resource managers and line managers who often implement personnel programs and policies. <br/><br/> Human Resource Management takes a managerial orientation and is viewed as being relevant to managers in every unit, project or team. Managers are constantly faced with human relation management issues, problems and decision making. <br/><br/>  This course will introduce management students to HR activities (recruitment and selection, training and development, reward management and performance appraisal) with the emphasis that it is the bundles of HR interventions and not single practices that develop effective human resources. Based on realistic, current trends confronting managers, this course will also expose the management students to contemporary issues, such as, employee engagement and organizational change, and how HR can work together with knowledge management, technology, internationalization - and talent management - to further develop the effectiveness of human resources and help create, develop and sustain a high-performing organization",
                    link: "https://youtu.be/CebD5PCML6w?si=qbXiR8r9eztjvUvM",
                    credit: 6,
                },
                {
                    name: "Organisational Behaviour",
                    description:
                        "This course explores the principles and practices of entrepreneurship and innovation. Students will learn how to identify and evaluate entrepreneurial opportunities, develop innovative ideas, and create viable business models. The course emphasizes critical thinking, creativity, and problem-solving skills necessary for successful entrepreneurship. Students will also gain an understanding of the challenges and strategies involved in launching and growing a new venture in a dynamic business environment",
                    link: "https://youtu.be/t1g7aCRoC-I?si=5luqjqunPivUr5wL",
                    credit: 6,
                },
                {
                    name: "Global Marketing",
                    description:
                        "This course systematically unfolds crucial issues in corporate finance management at graduate level. We review the introductory aspect of the course, and build from there toward approaches of investment appraisals, explaining details as regards the methods like compound interest, net present value decision rule and the internal rate of return. Additionally, methods such as the annuities and perpetuities, sinking fund and discounted cash flows and tax payments would be fully discussed. <br/><br/> Issues in capital rationing and risk analysis are discussed under certain market conditions, considering the constraint, sensitivity analysis. <br/><br/> Our study efforts will also consider portfolio theory and capital asset pricing model, which is an important aspect of corporate financial management, together with the capital budgeting model and the associated specific tools used in portfolio theory and pricing decision. <br/><br/> Other topics such as market share value and pricing would be examined to give students advanced knowledge of the company‘s worth. We further go on to dividend decision, which is the crux of cash management concern, as against retained earning for futures operations and the rule of safety thumps",
                    link: "https://youtu.be/zk1-2CERWHs?si=tT-pLAUoA5iI_b3h",
                    credit: 6,
                },
                {
                    name: "AI in Business",
                    description:
                        "This course is designed to provide students with an understanding of the interplay between politics, economics, and geography in shaping international relations. Students will learn about the key concepts and theories of geopolitics and geoeconomics, as well as their practical applications in contemporary world affairs",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Project Management",
                    description:
                        "The course will deepen the student’s comprehension of the digital transformation that is pervading society. The course will examine the process that contributes to the transitions to a digital society and economy. Therefore, digitalization is having an impact on different levels, affecting structures and the strategies of the organization, their ability to respond to external stimuli, the interactions with the stakeholders and the definition of the value proposition. Hence, the Digital transformation course aims also to support the students to understand the problems that the digital transformation might arise and, at the same time, to assess the new opportunities that the digitalization produces",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Innovation & Design Thinking",
                    description:
                        "This course introduces the key concepts, tools, and principles of strategy formulation and competitive analysis. It is concerned with managerial decisions and actions that affect the performance and survival of business enterprises. The course is focused on the information, analyses, organizational processes, and skills and business judgment managers must use to devise strategies, position their businesses, define firm boundaries and maximize long-term profits in the face of uncertainty and competition.  The course takes a general management perspective, viewing the firm as a whole, and examining how policies in each functional area are integrated into an overall competitive strategy. The key strategic business decisions of concern in this course involve choosing competitive strategies, creating competitive advantages, taking advantage of external opportunities, securing and defending sustainable market positions, and allocating critical resources over long periods. Decisions such as these can only be made effectively by viewing a firm holistically, and over the long term",
                    link: "https://youtu.be/wVY3uLMG-Fk?si=rTdwcjAk6-tEgbqS",
                    credit: 6,
                },
                {
                    name: "Total Quality Management",
                    description:
                        "Project management is a key contributor to organizational success and business results. This course teaches the fundamentals of project management. Students will learn how to select projects (portfolio management), lead and manage projects, build project teams, handle project risks, manage costs and schedules, control project progress, terminate projects, and integrate sustainability considerations throughout the project life cycle",
                    link: "https://youtu.be/MMYVUtpiAPk?si=R1XbXLyILON4-yK6",
                    credit: 6,
                },
                {
                    name: "Change Management",
                    description:
                        "This course provides a comprehensive understanding of supply chain management, covering the key concepts, strategies, and practices involved in managing the flow of goods and services from suppliers to end customers. Students will explore various aspects of supply chain management, including procurement, logistics, inventory management, demand forecasting, and supplier relationships. The course aims to develop students' skills in designing, optimizing, and coordinating supply chain activities to achieve competitive advantage and operational excellence",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Management Information Systems",
                    description:
                        "This course approaches the Cross-Cultural Leadership from an organisational perspective, where individuals from different cultures, religions, and educational backgrounds interact on a daily basis. The organisation, where decisions are made collectively, promote people interactions and responsibilities incorporating the sense of importance of multiculturalism into its strategic vision. <br/><br/>  This course aims to develop an easily applied and solid rule to identify cross-cultural differences within the organisation that can create robust boundaries and link them to the effective attainment of group goals. At the same time, the course observes real-life cases where meaningful differences exist but address appropriately through leadership of cultural diversity",
                    link: "",
                    credit: 6,
                },
                {
                    name: "Capstone Project",
                    description: "...",
                    link: "",
                    credit: 6,
                },
            ],
        },
    },
    graduation_ceremony: [
        {
            title: "Lễ tốt nghiệp",
            location: "Tp. Hồ Chí Minh",
            avatar:
                "./assets/ltn27122025.webp",
            school: "Swiss UMEF",
            name: "MBA/EMBA",
            time: "27/12/2025",
            link: "https://www.facebook.com/ideas.edu.vn/posts/pfbid034nzCDGcFVfz54M62b4Yod9iJ3mMx2eVNMXB33PpDeDSw6Xw1cZsH4oucpX2TogDcl?locale=vi_VN",
        },
        {
            title: "Lễ tốt nghiệp",
            location: "Geneva - Thụy Sĩ",
            avatar:
                "./assets/ltnumef10202501.webp",
            school: "Swiss UMEF",
            name: "MBA/EMBA/MSc AI",
            time: "29/10/2025",
            link: "http://ideas.edu.vn/chuyen-di-thang-10-2025",
        },
        {
            title: "Lễ tốt nghiệp",
            location: "Eden Star Hotel - HCMC",
            avatar: "./assets/ltn72025.webp",
            school: "Ascencia Business School",
            name: "Global MBA - DBA",
            time: "26/07/2025",
            link: "./assets/ltn72025.webp",
        },
        {
            title: "Lễ tốt nghiệp",
            location: "Paris - Pháp",
            avatar:
                "./assets/quangnon_cdp.webp",
            school: "Ascencia Business School",
            name: "Global MBA - DBA",
            time: "02/07/2025",
            link: "https://www.facebook.com/ideas.edu.vn/posts/pfbid02imDSb2CKDPVgKCQtkQTfihwpayYVVjuunvfDfRWfxTMstTD661BnZmYygnTwt9wpl?locale=vi_VN",
        },
        {
            title: "Lễ tốt nghiệp",
            location: "IDEAS - Việt Nam",
            avatar:
                "./assets/8X1A9328-1-1.jpg",
            school: "Ascencia Business School",
            name: "Global MBA - DBA",
            time: "23/11/2024",
            link: "https://youtu.be/hmVxOq5jkeM?si=gR-YOgFi2KQJftr9",
        },
        {
            title: "Lễ tốt nghiệp",
            location: "IDEAS - Việt Nam",
            avatar:
                "https://ideas.edu.vn/wp-content/uploads/2024/10/Totnghiepumef.jpg",
            school: "Swiss UMEF",
            name: "EMBA & Online MBA",
            time: "26/10/2024",
            link: "https://youtu.be/fBf5YcaMxDY?si=eJDfqKWc4HxT_TmS",
        },
        {
            title: "Lễ tốt nghiệp",
            location: "Paris - Pháp",
            avatar: "https://ideas.edu.vn/wp-content/new_public/data_imgs/image.png",
            school: "Ascencia Business School",
            name: "Global MBA - DBA",
            time: "18/07/2024",
            link: "https://www.facebook.com/share/r/14UHtxBTZQ/",
        },
        {
            title: "Lễ tốt nghiệp",
            location: "IDEAS - Việt Nam",
            avatar:
                "https://ideas.edu.vn/wp-content/uploads/2024/01/416256674_837845658141991_5379123310787471174_n.jpg",
            school: "Ascencia Business School",
            name: "Global MBA - DBA",
            time: "06/01/2024",
            link: "https://youtu.be/Dc78ClToNRo?si=kfg00KZ6gYpOWwTI",
        },
    ],
    student_quote: [
        {
            name: "Nguyễn Thanh Bình",
            title:
                "Tiến sĩ QTKD Pháp – Ascencia Business School - Director of Information & Environment technology application Institute",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/02/casc1.jpg",
            content:
                "Cảm ơn sự hỗ trợ rất nhiệt tình của đội ngũ cán bộ và nhân viên của IDEAS, đã đồng hành cùng chúng tôi trong suốt quá trình nghiên cứu, hỗ trợ không quản ngày đêm để chúng tôi hoàn thành mục tiêu nghiên cứu Tiến Sĩ – cấp bậc cao nhất của Học vị",
        },
        {
            name: "Nguyễn Huỳnh Phương",
            title:
                "Thạc Sĩ QTKD Pháp (Global MBA) – Ascencia Business School - UNIT MANAGER in Hanwha Life Việt Nam",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/02/huynhphuong.jpg",
            content:
                "Đối với những bạn chọn chương trình trực tuyến, tôi khuyên bạn nên chọn nơi đáng tin cậy như IDEAS. Bạn nên chia sẻ mọi căng thẳng hoặc khó khăn với giảng viên vì họ sẽ đưa ra những gợi ý hữu ích để giúp bạn vượt qua những khó khăn đó",
        },
        {
            name: "Nguyễn Thị Hà Miên",
            title:
                "Thạc Sĩ QTKD Pháp (Global MBA) – Ascencia Business School - Deputy Project Manager",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/02/hamien.jpg",
            content:
                "Tôi chọn chương trình trực tuyến vì nó linh hoạt hơn. Bên cạnh đó, sự hỗ trợ 24/7 của IDEAS đã giúp tôi hoàn thành tốt nhất về thời hạn nộp bài tập liên tục cũng như nhắc nhở thường xuyên về lớp học",
        },
        {
            name: "Lê Ngọc Thương",
            title:
                "Thạc Sĩ QTKD Thụy Sĩ (Executive MBA) – Swiss UMEF Head of Commercial Operations – Boehringer Ingelheim",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/02/cumef.jpg",
            content:
                " Chương trình đã kết nối tôi với nhiều bạn cùng lớp ở các ngành nghề khác nhau, giúp tôi tiếp nhận những chia sẻ và kinh nghiệm quý báu. Tôi đặc biệt cảm ơn IDEAS vì sự hỗ trợ suốt 1 năm qua, từ kiến thức chuyên gia đến hỗ trợ hành chính 24/7, giúp tôi cân bằng việc học và công việc để đạt được mục tiêu",
        },
        {
            name: "Lê Chí Thành",
            title:
                "Tiến sĩ QTKD Pháp – Ascencia Business School - Channel Business Manager (Indochina) | Leica Biosystems (an OpCo of Danaher)",
            avatar: "https://ideas.edu.vn/wp-content/uploads/2025/02/casc2.jpg",
            content:
                "Hành trình học trực tuyến từ năm 2016 chương trình MBA và sau đó tiếp bước lựa chọn DBA của tôi luôn được hỗ trợ và đồng hành bởi đội ngũ của IDEAS. Có thể học online là sự lựa chọn phù hợp cho tôi để sắp xếp giữa công việc và gia đình. Lời cảm ơn sâu sắc của tôi gửi đến Dr. Pham Quang Vinh và đội ngũ hỗ trợ IDEAS, đã luôn động viên, nhắc nhở trong suốt chặng đường dài này",
        },
        {
            name: "Chu Hoàng Thái",
            title:
                "Thạc Sĩ QTKD Thụy Sĩ (Executive MBA) – Swiss UMEF Director Of Housekeeping – REGENT PHU QUOC",
            avatar:
                "https://ideas.edu.vn/wp-content/uploads/2025/02/chu_hoang_thai.jpg",
            content:
                "Lựa chọn chương trình EMBA của Swiss UMEF – hỗ trợ bởi IDEAS là một quyết định đúng đắn đối với tôi, vì nó thuận tiện cho việc vừa đi học vừa đi làm cũng như phù hợp về khả năng tài chính của tôi. Ngoài kiến thức từ các Giáo sư Thụy sĩ và châu âu, giá trị lớn nhất đối với tôi là mạng lưới quan hệ (networking)",
        },
    ],
    school: {
        "Swiss UMEF": {
            link: "https://www.swiss-umef.ch/en",
            logo: "https://ideas.edu.vn/wp-content/uploads/2025/07/2-MBA-European-Online-Ranking-1.webp",
            small_logo:
                "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcShSoIjIIW_XlfCq3nbCxt--s3zt2lxrO74_A&amp;s",
        },
        "Ascencia Business School": {
            link: "https://www.ascencia-business-school.com/en/",
            logo: "https://ideas.edu.vn/wp-content/uploads/2024/03/Logo-Ascencia-Business-School-1.png",
            small_logo:
                "https://ideas.edu.vn/wp-content/new_public/data_imgs/Ascencia-Favicon.png",
        },
        "College de Paris": {
            link: "https://www.collegedeparis.fr/",
            logo: "https://www.collegedeparis.fr/wp-content/uploads/2021/06/cdp-formation-continue.png",
            small_logo:
                "https://ideas.edu.vn/wp-content/new_public/data_imgs/Ascencia-Favicon.png",
        },
        "Estiam & RB College": {
            link: "https://www.estiam.education/",
            logo: "https://ideas.edu.vn/wp-content/uploads/2025/03/estiam.png",
            small_logo:
                "https://ideas.edu.vn/wp-content/uploads/2025/10/small_estiam.webp",
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
            q: "Đây có phải là chương trình liên kết đào tạo giữa IDEAS và các Trường không?",
            a: "<b>Không,</b> <br/>Khi bắt đầu học một chương trình bạn sẽ chính thức là sinh viên của trường. IDEAS không trực tiếp liên kết đào tạo với các trường mà đóng vai trò cầu nối, là đơn vị tư vấn và hỗ trợ học thuật trong suốt quá trình học và nhận bằng. <br/> <br/> IDEAS giúp học viên tiếp cận các <b>chương trình chính thức</b> từ các trường đại học quốc tế, đồng thời hỗ trợ trong suốt quá trình học và giúp giải quyết các vấn đề phát sinh (nếu có) để đảm bảo học viên có trải nghiệm học tập hiệu quả và thuận lợi nhất. <br/> Toàn bộ chương trình học, bằng cấp và chứng chỉ đều do các trường cấp trực tiếp và có <b>chứng nhận hợp pháp hoá lãnh sự của Đại sứ quán Việt Nam</b> ở quốc gia có trụ sở chính của trường.",
        },
        {
            q: "IDEAS có tổ chức lớp học trực tiếp không hay chỉ hỗ trợ học online?",
            a: "Các chương trình do IDEAS hỗ trợ chủ yếu được triển khai theo hình thức học trực tuyến, giúp học viên linh hoạt về thời gian và địa điểm. Tuy nhiên, IDEAS cũng thường xuyên tổ chức các buổi workshop, lớp chuyên đề và sự kiện kết nối trực tiếp để học viên có thể giao lưu và học hỏi thêm.",
        },
        {
            q: "Lớp chuyên đề của IDEAS được tổ chức như thế nào và có bắt buộc không?",
            a: "Lớp chuyên đề của IDEAS được tổ chức theo hình thức trực tuyến, do các giảng viên hoặc chuyên gia trong ngành hướng dẫn. Nội dung các buổi chuyên đề tập trung vào việc bổ trợ kiến thức, giải đáp thắc mắc và giúp học viên hiểu sâu hơn về môn học, cũng như áp dụng vào thực tiễn. Lớp chuyên đề không bắt buộc và không ảnh hưởng đến điểm số của chương trình, tuy nhiên IDEAS khuyến khích các học viên tham gia để có thể nâng cao kiến thức và đạt kết quả học tập tốt hơn. Đây cũng là cơ hội để học viên trao đổi trực tiếp với chuyên gia và giảng viên, kết nối với bạn bè là các cấp quản lý và những nhà lãnh đạo cùng khóa và nhận được những chia sẻ hữu ích từ thực tế công việc.",
        },
    ],
};