CREATE TABLE [dbo].[Actelion2017](
[注册用户] [nvarchar](255) NULL,
[F2] [nvarchar](255) NULL,
[F3] [nvarchar](255) NULL,
[F4] [nvarchar](255) NULL,
[F5] [nvarchar](255) NULL,
[F6] [nvarchar](255) NULL,
[F7] [float] NULL,
[F8] [nvarchar](255) NULL,
[F9] [datetime] NULL,
[F10] [nvarchar](255) NULL
) ON [PRIMARY]

CREATE TABLE [dbo].[Answer](
[Id] [int] IDENTITY(1,1) NOT NULL,
[ProblemId] [int] NOT NULL,
[Name] [nvarchar](250) NOT NULL,
)

CREATE TABLE [dbo].[APP_Admin](
[Id] [int] IDENTITY(1,1) NOT NULL,
[Name] [nvarchar](50) NOT NULL,
[PassWord] [nvarchar](50) NOT NULL,
[Type] [int] NOT NULL,
[UserId] [int] NULL
)

CREATE TABLE [dbo].[CallHistory](
[Id] [int] IDENTITY(1,1) NOT NULL,
[PatientId] [int] NULL,
[InsertTime] [datetime] NULL CONSTRAINT [DF_CallHistory_InsertTime]  DEFAULT (getdate()),
)

CREATE TABLE [dbo].[City](
[cityID] [int] IDENTITY(1,1) NOT NULL,
[cityName] [varchar](50) NOT NULL,
[proID] [int] NULL
)

CREATE TABLE [dbo].[CRM](
[Region] [varchar](100) NULL,
[Sales Rep] [varchar](100) NULL,
[Physicians] [varchar](100) NULL,
[Hospital Name] [varchar](100) NULL,
[Province] [varchar](100) NULL
)

CREATE TABLE [dbo].[Department](
[Id] [int] IDENTITY(1,1) NOT NULL,
[Name] [varchar](250) NULL,
)

CREATE TABLE [dbo].[Doctor](
[Id] [int] IDENTITY(1,1) NOT NULL,
[HospitalId] [int] NULL,
[Name] [nvarchar](50) NULL,
[Phone] [nvarchar](50) NULL,
[Department] [nvarchar](250) NULL,
[Email] [nvarchar](250) NULL,
[InsertTime] [datetime] NULL CONSTRAINT [DF_Doctor_InsertTime]  DEFAULT (getdate()),
[PassWord] [nvarchar](50) NULL,
[AdminId] [int] NULL,
[Sign] [bit] NULL,
)

CREATE TABLE [dbo].[DoctorLogin](
[Id] [int] IDENTITY(1,1) NOT NULL,
[DoctorId] [int] NULL,
[LoginTime] [datetime] NULL,
)

CREATE TABLE [dbo].[fcrm](
[Region] [varchar](100) NULL,
[Sales Rep] [varchar](1000) NULL,
[Physicians	] [varchar](100) NULL,
[Hospital Name] [varchar](1000) NULL,
[Province] [varchar](1000) NULL
)

CREATE TABLE [dbo].[FollowUpType](
[Id] [int] IDENTITY(1,1) NOT NULL,
[Name] [nvarchar](50) NOT NULL,
[PId] [int] NOT NULL,
[Grade] [int] NOT NULL,
[Sort] [int] NULL,
)

CREATE TABLE [dbo].[FollowUpTypeList](
[Id] [int] IDENTITY(1,1) NOT NULL,
[FId1] [int] NOT NULL,
[Name1] [nvarchar](50) NOT NULL,
[FId2] [int] NOT NULL,
[Name2] [nvarchar](50) NOT NULL,
[FId3] [int] NOT NULL,
[Name3] [nvarchar](50) NOT NULL,
[FId4] [int] NOT NULL,
[Name4] [nvarchar](50) NOT NULL,
[FType] [int] NULL,
[Other] [nvarchar](250) NULL,
[InsertTime] [datetime] NULL CONSTRAINT [DF_FollowUpTypeList_InsertTime]  DEFAULT (getdate()),
[FId5] [int] NULL,
[Name5] [nvarchar](50) NULL,
)

CREATE TABLE [dbo].[FOSTER_User](
[Id] [int] IDENTITY(1,1) NOT NULL,
[Name] [nvarchar](50) NULL,
[Phone] [nvarchar](50) NULL,
[Hosptial] [nvarchar](50) NULL,
[Department] [nvarchar](50) NULL,
[Email] [nvarchar](100) NULL,
[InsertTime] [datetime] NULL CONSTRAINT [DF_FOSTER_User_InsertTime]  DEFAULT (getdate()),
[Xuan] [bit] NULL,
[Get] [bit] NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[Hospital](
[Id] [int] IDENTITY(1,1) NOT NULL,
[cityName] [varchar](250) NOT NULL,
[proName] [varchar](250) NOT NULL,
[Name] [nvarchar](250) NOT NULL,
[SalesRep] [varchar](250) NULL,
[Region] [varchar](50) NULL,
[Code] [int] NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[HospitalPharmacyHistory](
[Id] [int] IDENTITY(1,1) NOT NULL,
[PatientId] [int] NULL,
[InsertTime] [datetime] NULL CONSTRAINT [DF_HospitalPharmacyHistory_InsertTime]  DEFAULT (getdate()),
[Type] [nvarchar](10) NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[IncomingCalls](
[Id] [int] IDENTITY(1,1) NOT NULL,
[AdminId] [int] NOT NULL,
[InsertTime] [datetime] NOT NULL,
[Text] [nvarchar](2000) NULL,
[PatientId] [int] NOT NULL,
[FollowUpTypeId] [nvarchar](100) NULL,
[IsSolve] [bit] NULL,
[ProblemType] [int] NULL,
[AnswerType] [int] NULL,
[ProblemTypeOther] [nvarchar](100) NULL,
[AnswerTypeOther] [nvarchar](100) NULL,
[ProblemSource] [int] NULL,
[ProcessType] [int] NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[MyCase](
[Id] [int] IDENTITY(1,1) NOT NULL,
[IsFirst] [bit] NULL,
[PatientId] [int] NOT NULL,
[InsertTime] [datetime] NOT NULL CONSTRAINT [DF_MyCase_InsertTime]  DEFAULT (getdate()),
[Number] [int] NULL,
[CaseTime] [datetime] NULL,
[Hospital] [nvarchar](50) NULL,
[Department] [nvarchar](50) NULL,
[Doctor] [nvarchar](50) NULL,
[MedicalResult1] [nvarchar](50) NULL,
[MedicalResult2] [nvarchar](50) NULL,
[MedicineName1] [nvarchar](50) NULL,
[MedicineYear1] [nvarchar](50) NULL,
[MedicineMouth1] [nvarchar](50) NULL,
[MedicineIsStop1] [nvarchar](50) NULL,
[MedicineStopWhy1] [nvarchar](50) NULL,
[MedicineIsChange1] [nvarchar](50) NULL,
[MedicineName2] [nvarchar](50) NULL,
[MedicineYear2] [nvarchar](50) NULL,
[MedicineMouth2] [nvarchar](50) NULL,
[MedicineIsStop2] [nvarchar](50) NULL,
[MedicineStopWhy2] [nvarchar](50) NULL,
[MedicineIsChange2] [nvarchar](50) NULL,
[MedicineName3] [nvarchar](50) NULL,
[MedicineYear3] [nvarchar](50) NULL,
[MedicineMouth3] [nvarchar](50) NULL,
[MedicineIsStop3] [nvarchar](50) NULL,
[MedicineStopWhy3] [nvarchar](50) NULL,
[MedicineIsChange3] [nvarchar](50) NULL,
[MedicineName4] [nvarchar](50) NULL,
[MedicineYear4] [nvarchar](50) NULL,
[MedicineMouth4] [nvarchar](50) NULL,
[MedicineIsStop4] [nvarchar](50) NULL,
[MedicineStopWhy4] [nvarchar](50) NULL,
[MedicineIsChange4] [nvarchar](50) NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[Patient](
[Id] [int] IDENTITY(1,1) NOT NULL,
[Name] [nvarchar](50) NULL,
[Phone] [nvarchar](50) NULL,
[Address] [nvarchar](250) NULL,
[Promary] [nvarchar](20) NULL,
[City] [nvarchar](20) NULL,
[Hospital] [int] NULL,
[Department] [nvarchar](250) NULL,
[Doctor] [int] NULL,
[IsSelf] [int] NULL,
[AdminId] [int] NULL,
[IsRecord] [int] NULL,
[RecordImageTime] [datetime] NULL,
[FollowUpTime] [datetime] NULL,
[FollowUpId] [int] NULL,
[FollowUpType] [int] NULL,
[FollowUpCount] [int] NULL,
[FollowUpNextTime] [datetime] NULL,
[Type] [int] NOT NULL CONSTRAINT [DF_Patient_Type]  DEFAULT ((0)),
[Email] [nvarchar](250) NULL,
[RegisterTime] [datetime] NULL CONSTRAINT [DF_Patient_RegisterTime]  DEFAULT (getdate()),
[PassWord] [nvarchar](250) NULL,
[Ruzutujing] [int] NULL,
[DropType] [nvarchar](250) NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[Pdf](
[Id] [int] IDENTITY(1,1) NOT NULL,
[Url] [nvarchar](500) NULL,
[Image] [nvarchar](500) NULL,
[Title] [nvarchar](500) NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[PdfDoc](
[ID] [int] IDENTITY(1,1) NOT NULL,
[Name] [nvarchar](100) NULL,
[HospitalName] [nvarchar](100) NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[PDFScore](
[Id] [int] IDENTITY(1,1) NOT NULL,
[StandardId] [int] NULL,
[Score] [int] NULL,
[TotalScoreID] [int] NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[PDFStandard](
[Id] [int] IDENTITY(1,1) NOT NULL,
[Model] [nvarchar](100) NULL,
[Title] [nvarchar](100) NULL,
[Contents] [nvarchar](100) NULL,
[MaxScore] [int] NULL,
[Used] [bit] NULL,
[OrderId] [int] NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[PdfToDoc](
[Id] [int] IDENTITY(1,1) NOT NULL,
[DocId] [int] NULL,
[PdfId] [int] NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[PdfTotalScore](
[Id] [int] IDENTITY(1,1) NOT NULL,
[DocId] [int] NULL,
[TotalScore] [int] NULL,
[PdfId] [int] NULL,
[InsertTime] [datetime] NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[Problem](
[Id] [int] IDENTITY(1,1) NOT NULL,
[Name] [nvarchar](250) NOT NULL,
[MultipleChoice] [int] NULL,
[ParentId] [int] NOT NULL CONSTRAINT [DF_Problem_ParentId]  DEFAULT ((0)),
) ON [PRIMARY]

CREATE TABLE [dbo].[ProblemList](
[Id] [int] IDENTITY(1,1) NOT NULL,
[ProblemParentId] [int] NOT NULL,
[ProblemId] [int] NOT NULL,
[AnswerId] [int] NULL,
[Text] [nvarchar](500) NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[ProblemParents](
[Id] [int] IDENTITY(1,1) NOT NULL,
[ProblemId] [int] NOT NULL,
[AdminId] [int] NOT NULL,
[PatientId] [int] NOT NULL,
[InsertTime] [datetime] NOT NULL,
[UpdateTime] [datetime] NOT NULL,
[Type] [int] NOT NULL,
[FinishType] [int] NOT NULL,
[FollowUpTypeId] [nvarchar](500) NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[ProblemSetUp](
[Id] [int] IDENTITY(1,1) NOT NULL,
[Numeber] [int] NOT NULL,
[IntervalTime] [int] NOT NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[ProblemSetUpList](
[Id] [int] NOT NULL,
[ProblemSetUpId] [int] NOT NULL,
[ProblemId] [int] NOT NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[Promary](
[proID] [int] IDENTITY(1,1) NOT NULL,
[proName] [varchar](50) NOT NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[RecordImage](
[Id] [int] IDENTITY(1,1) NOT NULL,
[PatientId] [int] NOT NULL,
[UploadTime] [datetime] NULL,
[CheckTime] [datetime] NULL,
[Type] [int] NOT NULL CONSTRAINT [DF_Record_Type]  DEFAULT ((0)),
[Url] [nvarchar](250) NULL,
[AdminId] [int] NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[Reminder](
[Id] [int] IDENTITY(1,1) NOT NULL,
[RemindType] [int] NULL,
[RemindTime] [datetime] NULL,
[Place] [nvarchar](100) NULL,
[Event] [nvarchar](500) NULL,
[PatientId] [int] NOT NULL,
[InsertTime] [datetime] NOT NULL CONSTRAINT [DF_Reminder_InsertTime]  DEFAULT (getdate()),
[OpenId] [nvarchar](100) NULL,
[EveryDay] [bit] NULL,
[Completion] [int] NULL,
[UserName] [nvarchar](50) NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[Score](
[Id] [int] IDENTITY(1,1) NOT NULL,
[ScoreCount] [int] NOT NULL,
[PatientId] [int] NOT NULL,
[InsertTime] [datetime] NOT NULL CONSTRAINT [DF_Score_InsertTime]  DEFAULT (getdate()),
[Type] [nvarchar](50) NOT NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[Shop](
[Id] [int] IDENTITY(1,1) NOT NULL,
[PatientId] [int] NULL,
[InsertTime] [datetime] NULL,
[Name] [nvarchar](50) NULL,
[Phone] [nvarchar](50) NULL,
[Address] [nvarchar](250) NULL,
[Describe] [nvarchar](250) NULL,
[Complete] [int] NULL,
[ScoreCount] [int] NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[Symptomatography](
[Id] [int] IDENTITY(1,1) NOT NULL,
[InsertTime] [datetime] NOT NULL CONSTRAINT [DF_Symptomatography_InsertTime]  DEFAULT (getdate()),
[PatientId] [int] NOT NULL,
[BP] [nvarchar](50) NULL,
[Weight] [nvarchar](50) NULL,
[AnhelationDyspnea] [nvarchar](50) NULL,
[ChestDistressChestPain] [nvarchar](50) NULL,
[Dizziness] [nvarchar](50) NULL,
[Syncope] [nvarchar](50) NULL,
[Fatigue] [nvarchar](50) NULL,
[Swellness] [nvarchar](50) NULL,
[Cyanosis] [nvarchar](50) NULL,
[Hemoptysis] [nvarchar](50) NULL,
[OtherSymptoms] [nvarchar](50) NULL,
[Relapse] [nvarchar](50) NULL,
[Fever] [nvarchar](50) NULL,
[Cough] [nvarchar](50) NULL,
[SputumColor] [nvarchar](50) NULL,
[SputumProperties] [nvarchar](50) NULL,
[SputumVolume] [nvarchar](50) NULL,
[Aggravation] [nvarchar](50) NULL,
[Diuretic] [nvarchar](50) NULL,
[Digoxin] [nvarchar](50) NULL,
[Anticoagulant] [nvarchar](50) NULL,
[CalciumIonAntagonist] [nvarchar](50) NULL,
[EndothelinAntagonist] [nvarchar](50) NULL,
[EndothelinAntagonistReason] [nvarchar](50) NULL,
[Prostaglandins] [nvarchar](50) NULL,
[ProstaglandinsReason] [nvarchar](50) NULL,
[Phosphodiesterase] [nvarchar](50) NULL,
[PhosphodiesteraseReason] [nvarchar](50) NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[WhereHospital](
[Id] [int] IDENTITY(1,1) NOT NULL,
[Promary] [nvarchar](50) NOT NULL,
[City] [nvarchar](50) NOT NULL,
[HospitalName] [nvarchar](500) NOT NULL,
[Adress] [nvarchar](500) NULL,
[Phone] [nvarchar](50) NULL,
) ON [PRIMARY]

CREATE TABLE [dbo].[WherePharmacy](
[Id] [int] IDENTITY(1,1) NOT NULL,
[Promary] [nvarchar](50) NOT NULL,
[City] [nvarchar](50) NOT NULL,
[PharmacyName] [nvarchar](500) NOT NULL,
[Adress] [nvarchar](500) NULL,
[Phone] [nvarchar](50) NULL,
[OrderId] [int] NULL,
) ON [PRIMARY]