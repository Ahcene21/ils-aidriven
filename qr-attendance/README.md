<div dir="rtl">

# الحضور بـ QR — إضافة ووردبريس لتسجيل حضور الطلبة وغياباتهم

إضافة (Plugin) لووردبريس تسجّل حضور الطلبة بمسح رمز QR:
يبدأ الأستاذ حصة ويعرض على الشاشة أو جهاز العرض رمز QR **يتجدّد كل بضع ثوانٍ**،
فيمسحه الطلبة بكاميرا هواتفهم ليُسجَّل حضورهم فورًا. عند غلق الحصة يُسجَّل
**غائبًا** تلقائيًا كلُّ طالب مسجَّل في المادة لم يسجّل حضوره.

الواجهة مترجمة بالكامل إلى العربية وتدعم الاتجاه من اليمين إلى اليسار (RTL).

## المزايا

**للأستاذ**

- إنشاء المواد (مع الرمز / الفوج) وتعيين أستاذ لكل مادة؛ لا يرى الأستاذ إلا مواده.
- إضافة الطلبة بالبريد أو اسم المستخدم أو رقم التسجيل، أو **إنشاء حساباتهم دفعة واحدة**
  بلصق ثلاثة أعمدة من Excel (رقم التسجيل، الاسم، البريد) مع إرسال رابط تعيين كلمة المرور.
- شاشة الحصة المباشرة: رمز QR كبير مع وضع **ملء الشاشة** لجهاز العرض، عدّاد تنازلي
  لتجديد الرمز، وقائمة الحضور تتحدّث تلقائيًا كل 3 ثوانٍ.
- تعديل حالة أي طالب يدويًا: حاضر / متأخر / غائب / غياب مبرَّر.
- **مسح بطاقات الطلبة**: بديل للقاعات دون جهاز عرض — الأستاذ يمسح بطاقة QR الشخصية لكل طالب.
- غلق الحصة يدويًا أو تلقائيًا بعد مدتها، وإمكانية إعادة فتحها.
- تقرير لكل مادة: جدول (الطلبة × الحصص) مع مجموع الحضور والتأخر والغياب ونسبة الحضور،
  وتمييز الطلبة الذين بلغوا حدّ الغيابات، و**تصدير CSV** يفتح مباشرة في Excel بالعربية.

**للطالب**

- مسح الرمز بتطبيق الكاميرا العادي في الهاتف — لا حاجة لتثبيت أي تطبيق.
- إن لم يكن مسجّل الدخول: يُتحقَّق من الرمز لحظة المسح ثم يُطلب منه تسجيل الدخول،
  ويُسجَّل حضوره بعد ذلك دون أن يؤثّر انتهاء صلاحية الرمز أثناء الدخول.
- صفحة «حضوري»: إحصائيات كل مادة وسجلّ الحصص وتنبيه عند بلوغ حدّ الغيابات.
- ماسح داخل الموقع وبطاقة QR شخصية.

**مكافحة الغش**

- الرمز المعروض موقَّع (HMAC) ويتغيّر كل 20 ثانية (قابل للتعديل)، فتفقد صورته
  المرسَلة إلى زميل غائب صلاحيتها في الحال.
- الهاتف الواحد لا يسجّل إلا طالبًا واحدًا في الحصة الواحدة.
- يُحفظ لكل تسجيل: الطريقة (QR / بطاقة / يدوي / تلقائي) والوقت وعنوان IP.

## التثبيت

1. اضغط مجلّد `qr-attendance` في ملف ZIP.
2. في لوحة ووردبريس: **إضافات ← أضف جديدًا ← رفع إضافة**، ثم فعّلها.
3. أنشئ صفحتين للطلبة (اختياري لكن مستحسن):
   - «حضوري» وتحتوي على: `[qra_my_attendance]` و`[qra_my_card]`
   - «مسح الحضور» وتحتوي على: `[qra_scanner]`
4. من **الحضور والغياب ← الإعدادات** اختر صفحة «حضوري» واضبط المدد.
5. امنح الأساتذة دور **أستاذ** (Teacher) من صفحة المستخدمين.

> ⚠️ الكاميرا داخل المتصفح لا تعمل إلا عبر **HTTPS**. أما المسح بتطبيق كاميرا الهاتف
> فيعمل في كل الأحوال.

## طريقة الاستعمال

1. **الحضور والغياب ← المواد**: أنشئ مادة ثم أضف طلبتها من تبويب «الطلبة».
2. في بداية الحصة اضغط **بدء حصة** ثم **ملء الشاشة** واعرضها على جهاز العرض.
3. يمسح الطلبة الرمز بهواتفهم؛ تظهر أسماؤهم مباشرة في القائمة.
4. عند النهاية اضغط **غلق الحصة** (أو تُغلق تلقائيًا)، فيُسجَّل الباقون غائبين.
5. لتبرير غياب: افتح الحصة من تبويب «الحصص» وغيّر حالة الطالب إلى «غياب مبرَّر».

## الرموز القصيرة (Shortcodes)

| الرمز | الوظيفة |
|---|---|
| `[qra_scanner]` | ماسح QR بالكاميرا داخل الموقع |
| `[qra_my_attendance]` | حضور الطالب وغياباته في كل مادة |
| `[qra_my_card]` | بطاقة QR الشخصية التي يمسحها الأستاذ |

## الإعدادات

| الإعداد | الافتراضي | الوصف |
|---|---|---|
| تجديد رمز QR | 20 ثانية | 0 = رمز ثابت طوال الحصة |
| متأخر بعد | 15 دقيقة | 0 = تعطيل التأخر |
| المدة الافتراضية للحصة | 90 دقيقة | تُغلق الحصة بعدها تلقائيًا |
| هاتف واحد لكل طالب | مفعّل | |
| التسجيل التلقائي في المادة | معطّل | لقبول أي طالب يمسح الرمز |
| إنذار الغياب | 3 غيابات | 0 = تعطيل |

</div>

---

## Technical notes (English)

- **Requirements:** WordPress 6.0+, PHP 7.4+.
- **Tables:** `{prefix}qra_courses`, `qra_enrollments`, `qra_sessions`, `qra_attendance`
  (UTC datetimes; one row per student per session, unique on `session_id, user_id`).
- **Roles / capability:** `qra_teacher` and `qra_student` roles; `qra_manage_attendance`
  capability (also granted to administrators). Administrators manage every course,
  teachers only their own.
- **Session code:** `<session>-<window>-<hmac16>`, where `window = floor(time / rotation)`,
  signed with a per-session secret plus `wp_salt('nonce')`. The current and previous
  windows are accepted. The QR encodes `https://site/?qra=<code>`.
- **Login round-trip:** an anonymous scan is validated immediately and stored as a
  single-use, device-bound claim (transient, 10 min) that is redeemed after login.
- **Absences** are written when a session closes (manually, when the page/REST sees it has
  expired, or by the hourly `qra_close_expired_sessions` cron). Reopening removes them.
- **REST (`qra/v1`):** `POST /checkin`, `GET /sessions/{id}/live`,
  `POST /sessions/{id}/card`, `POST /sessions/{id}/mark`.
- **Hooks:** `qra_checked_in( $session, $user_id, $status )`, `qra_session_closed( $session )`.
- **Scanning:** native `BarcodeDetector` where available, [jsQR](https://github.com/cozmo/jsQR)
  (Apache-2.0) otherwise; codes are drawn with
  [qrcode-generator](https://github.com/kazuhikoarase/qrcode-generator) (MIT). Both are
  bundled in `assets/vendor`, so nothing is loaded from a CDN.
- **Translations:** `languages/qr-attendance.pot`, Arabic in `qr-attendance-ar.po/.mo`.
- **Uninstall** keeps the data unless “Delete all courses and attendance records when the
  plugin is deleted” is ticked in the settings.
