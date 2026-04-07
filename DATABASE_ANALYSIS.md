# Educational Management System - Comprehensive Database Analysis

## Executive Summary

This is a **multi-table relational database** managing an educational institution with **18 entities** and **10 association tables**. The system models student enrollments, teacher assignments, evaluations, and academic performance tracking across academic years and terms.

---

## SECTION 1: PURE ENTITIES (21 ENTITIES)

These are independent entities that exist independently in the system:

### 1. **User**

- **Table**: `users`
- **Description**: System users (admin, teachers, secretaries)
- **Primary Key**: `id` (bigint)
- **Key Attributes**:
    - `name`, `email` (unique), `password`, `role` (admin/enseignant/secretaire)
    - `phone` (nullable), `email_verified_at` (nullable)
- **Cascade**: No cascade relationships
- **Special Note**: Base authentication entity

### 2. **Etablissement** (School/Institution)

- **Table**: `etablissements`
- **Description**: School institution/organization
- **Primary Key**: `id` (bigint)
- **Key Attributes**: `nom`, `adresse`, `telephone`, `logo`, `code_ecole` (unique), `slogan`
- **Special Note**: Identifier for the school system

### 3. **AnneeScolaire** (Academic Year)

- **Table**: `annee_scolaires`
- **Description**: Academic year (e.g., 2025-2026)
- **Primary Key**: `id` (bigint)
- **Key Attributes**: `libelle` (string), `date_debut`, `date_fin` (dates), `est_active` (boolean)
- **Cascade**: YES - deletes trimestres, classes, inscriptions, affectations
- **Special Note**: Central entity for all academic activities

### 4. **Cycle** (Educational Cycle)

- **Table**: `cycles`
- **Description**: Cycle within school system (e.g., Premier Cycle, Second Cycle)
- **Primary Key**: `id` (bigint)
- **Key Attributes**: `nom` (string)
- **Cascade**: YES - deletes niveaux

### 5. **Niveau** (Grade Level)

- **Table**: `niveaux`
- **Description**: Grade level within a cycle (e.g., 6ème, 5ème, Terminale)
- **Primary Key**: `id` (bigint)
- **Key Attributes**: `nom` (string)
- **Foreign Key**: `cycle_id` (NOT nullable) → **Cycle**
- **Cascade**: YES - deletes classes
- **Cardinality**: Cycle **(1,N)** ↔ Niveau **(1,1)**

### 6. **Classe** (Classroom/Section)

- **Table**: `classes`
- **Description**: A specific class/section within a academic level and year
- **Primary Key**: `id` (bigint)
- **Key Attributes**: `nom` (string)
- **Foreign Keys**:
    - `niveau_id` (NOT nullable) → **Niveau**
    - `annee_scolaire_id` (NOT nullable) → **AnneeScolaire**
    - `salle_id` (nullable, SET NULL) → **Salle**
- **Cascade**: YES - deletes salles, inscriptions, affectations
- **Cardinality**:
    - Niveau **(1,N)** ↔ Classe **(1,1)**
    - AnneeScolaire **(1,N)** ↔ Classe **(1,1)**

### 7. **Salle** (Physical Classroom/Room)

- **Table**: `salles`
- **Description**: Physical classroom (e.g., 6ème M1, 6ème M2 - section A, B, etc.)
- **Primary Key**: `id` (bigint)
- **Key Attributes**: `nom` (string)
- **Foreign Key**: `classe_id` (NOT nullable) → **Classe**
- **Cascade**: YES - deletes inscriptions, affectations, evaluations
- **Special Note**: Abstract classroom designation
- **Cardinality**: Classe **(1,N)** ↔ Salle **(1,1)**

### 8. **Matiere** (Subject/Course)

- **Table**: `matieres`
- **Description**: Academic subject/course
- **Primary Key**: `id` (bigint)
- **Key Attributes**: `nom` (string), `code` (unique string, e.g., MATH, ANG)
- **No Foreign Keys**: Independent entity
- **Cardinality to Classes**: Matiere **(0,N)** ↔ Classe **(0,N)** via **classe_matiere** pivot

### 9. **Enseignant** (Teacher)

- **Table**: `enseignants`
- **Description**: Teacher/Instructor in the system
- **Primary Key**: `id` (bigint)
- **Key Attributes**: `matricule` (unique), `specialite` (nullable)
- **Foreign Keys**:
    - `user_id` (NOT nullable) → **User** (CASCADE)
    - `departement_id` (nullable, SET NULL) → **Departement**
- **Cascade**: YES - user deletion cascades through enseignant
- **Special Note**: Extension of User entity with teaching-related data
- **Cardinality**:
    - User **(0,1)** ← Enseignant **(1,1)** (NOT all users are teachers)

### 10. **Eleve** (Student)

- **Table**: `eleves`
- **Description**: School student/pupil
- **Primary Key**: `id` (bigint)
- **Key Attributes**: `matricule` (unique), `nom`, `prenom`, `date_naissance`, `sexe` (enum M/F), `lieu_naissance`, `telephone_parent`, `adresse`, `photo`
- **Soft Deletes**: YES - uses soft delete instead of hard delete
- **No Foreign Keys**: Independent entity for student master data

### 11. **Trimestre** (Term/Quarter)

- **Table**: `trimestres`
- **Description**: Academic term/quarter within an academic year
- **Primary Key**: `id` (bigint)
- **Key Attributes**: `nom` (string, e.g., '1er Trimestre', '2e Trimestre', '3e Trimestre')
- **Foreign Key**: `annee_scolaire_id` (NOT nullable) → **AnneeScolaire** (CASCADE)
- **Cascade**: YES - deletes sequences, bilans, absences
- **Cardinality**: AnneeScolaire **(1,N)** ↔ Trimestre **(1,1)**

### 12. **Sequence** (Evaluation Sequence)

- **Table**: `sequences`
- **Description**: Subdivision of a trimestre (e.g., Seq1, Seq2, Seq3)
- **Primary Key**: `id` (bigint)
- **Key Attributes**: `nom` (string)
- **Foreign Key**: `trimestre_id` (NOT nullable) → **Trimestre** (CASCADE)
- **Cascade**: YES - deletes evaluations, appreciations, moyennes, absences
- **Cardinality**: Trimestre **(1,N)** ↔ Sequence **(1,1)**

### 13. **Departement** (Teacher Department)

- **Table**: `departements`
- **Description**: Academic department where teachers are organized
- **Primary Key**: `id` (bigint)
- **Key Attributes**: `nom` (unique), `code` (nullable, e.g., INF), `description` (nullable)
- **No Cascade**: Deletes handled with SET NULL
- **Cardinality**: Departement **(1,N)** ↔ Enseignant **(0,N)** (teacher assignment to dept is optional)

---

## SECTION 2: ASSOCIATIONS WITH THEIR OWN ATTRIBUTES (10 ASSOCIATION TABLES)

These tables represent relationships between entities AND carry their own business attributes:

### Association 1: **Inscription** (Student Enrollment)

- **Table**: `inscriptions`
- **Purpose**: Links students to classes for a given academic year
- **Business Attributes** (Make this a WEAK ENTITY, not just a junction):
    - `date_inscription` (date, DEFAULT: now())
    - `statut` (string: 'actif', 'abandon', 'exclu')
    - `numero_recu` (nullable string) - Receipt number for fee tracking
- **Foreign Keys**:
    - `eleve_id` (NOT nullable) → **Eleve** (CONSTRAIN)
    - `classe_id` (NOT nullable) → **Classe** (CONSTRAIN)
    - `annee_scolaire_id` (NOT nullable) → **AnneeScolaire** (CONSTRAIN)
- **Unique Constraint**: `(eleve_id, annee_scolaire_id)` - Student can only be enrolled once per academic year
- **Cardinality Analysis**:
    - Eleve **(1,N)** ↔ Inscription **(1,1)** - One student, many inscriptions across years
    - Classe **(1,N)** ↔ Inscription **(0,N)** - One class, many student enrollments
    - AnneeScolaire **(1,N)** ↔ Inscription **(1,1)** - One year, many enrollments
- **Is This an Entity or Association?**: **WEAK ENTITY** because it has business attributes and identifies enrollments

---

### Association 2: **Affectation** (Teacher-Subject Assignment to Class)

- **Table**: `affectations`
- **Purpose**: Assigns teachers to teach specific subjects in specific classes for an academic year
- **Business Attributes** (MINIMAL):
    - Only timestamps (created_at, updated_at)
    - Primarily a linking table
- **Foreign Keys**:
    - `enseignant_id` (NOT nullable) → **Enseignant** (CONSTRAIN)
    - `matiere_id` (NOT nullable) → **Matiere** (CONSTRAIN)
    - `classe_id` (NOT nullable) → **Classe** (CONSTRAIN)
    - `niveau_id` (NOT nullable) → **Niveau** (CONSTRAIN)
    - `annee_scolaire_id` (NOT nullable) → **AnneeScolaire** (CONSTRAIN)
- **Unique Constraint**: `(matiere_id, classe_id, annee_scolaire_id)` - One teacher per subject per class per year (attribution_unique)
- **Cardinality Analysis**:
    - Enseignant **(1,N)** ↔ Affectation **(0,N)** - Teacher teaches multiple classes/subjects
    - Matiere **(1,N)** ↔ Affectation **(0,N)** - Subject taught in multiple classes
    - Classe **(1,N)** ↔ Affectation **(0,N)** - Class has multiple subject teachers
    - Niveau **(1,N)** ↔ Affectation **(0,N)** - Level has many affectations
    - AnneeScolaire **(1,N)** ↔ Affectation **(0,N)** - Year has many affectations
- **Is This an Entity?**: **PURE ASSOCIATION** - No business attributes, just linking entities

---

### Association 3: **Classe_Matiere** (Curriculum Definition - Pivot)

- **Table**: `classe_matiere`
- **Purpose**: Defines which subjects are taught in which classes and their coefficients
- **Business Attributes**:
    - `coefficient` (integer, DEFAULT: 1) - Weight of subject in class
    - `ordre` (integer, DEFAULT: 1) - Display order on student reports
- **Foreign Keys**:
    - `classe_id` (NOT nullable) → **Classe** (CASCADE)
    - `matiere_id` (NOT nullable) → **Matiere** (CASCADE)
- **Cardinality Analysis**:
    - Classe **(1,N)** ↔ Classe_Matiere **(1,1)**
    - Matiere **(1,N)** ↔ Classe_Matiere **(1,1)**
- **Is This an Entity?**: **ASSOCIATION WITH ATTRIBUTES** - Represents curriculum mapping

---

### Association 4: **Coefficients** (Subject Coefficient per Class)

- **Table**: `coefficients`
- **Purpose**: Alternative/Redundant way to store subject coefficients by class (DUPLICATE of classe_matiere.coefficient)
- **Business Attributes**:
    - `valeur` (integer, DEFAULT: 1) - Coefficient value
- **Foreign Keys**:
    - `matiere_id` (NOT nullable) → **Matiere**
    - `classe_id` (NOT nullable) → **Classe**
- **⚠️ WARNING**: REDUNDANT with `classe_matiere.coefficient` - Data duplication risk
- **Cardinality Analysis**:
    - Matiere **(1,N)** ↔ Coefficients **(0,N)**
    - Classe **(1,N)** ↔ Coefficients **(0,N)**
- **Recommendation**: Consider removing this table and using `classe_matiere` only

---

### Association 5: **Evaluation** (Assessment Definition)

- **Table**: `evaluations`
- **Purpose**: Defines evaluations/tests for a subject in a sequence for a class
- **Business Attributes**:
    - `titre` (string) - Name/title of the evaluation
    - `date_evaluation` (nullable date) - When the evaluation occurs
- **Foreign Keys**:
    - `sequence_id` (NOT nullable) → **Sequence** (CASCADE)
    - `matiere_id` (NOT nullable) → **Matiere** (CONSTRAIN)
    - `classe_id` (NOT nullable) → **Classe** (CONSTRAIN)
    - `enseignant_id` (NOT nullable) → **Enseignant** (CONSTRAIN)
- **Cascade**: YES on sequence deletion
- **Cardinality Analysis**:
    - Sequence **(1,N)** ↔ Evaluation **(0,N)** - One sequence, multiple evaluations
    - Matiere **(1,N)** ↔ Evaluation **(0,N)** - One subject, multiple evaluations across sequences
    - Classe **(1,N)** ↔ Evaluation **(0,N)** - One class, multiple evaluations
    - Enseignant **(1,N)** ↔ Evaluation **(0,N)** - Teacher creates multiple evaluations
- **Is This an Entity?**: **WEAK ENTITY/ASSOCIATION** - Represents assessment events with data

---

### Association 6: **Note** (Individual Student Grade)

- **Table**: `notes`
- **Purpose**: Records a student's score on a specific evaluation
- **Business Attributes**:
    - `valeur` (decimal 4,2) - Score out of 20 (e.g., 15.50)
    - `est_validee` (boolean, DEFAULT: false) - Whether grade is validated
- **Foreign Keys**:
    - `evaluation_id` (NOT nullable) → **Evaluation** (CONSTRAIN)
    - `inscription_id` (NOT nullable) → **Inscription** (CONSTRAIN)
- **Cardinality Analysis**:
    - Evaluation **(1,N)** ↔ Note **(0,N)** - One evaluation, many student grades
    - Inscription **(1,N)** ↔ Note **(0,N)** - One student enrollment, many grades across evaluations
- **Is This an Entity?**: **WEAK ENTITY** - Represents actual grade records with measured values

---

### Association 7: **Appreciation** (Behavioral/Work Assessment)

- **Table**: `appreciations`
- **Purpose**: Records qualitative assessments for a student in a sequence
- **Business Attributes**:
    - `travail` (string) - Work/effort assessment
    - `discipline` (string) - Discipline assessment
    - `conduite` (string) - Conduct assessment
    - `commentaire` (nullable text) - Teacher comments
- **Foreign Keys**:
    - `sequence_id` (NOT nullable) → **Sequence** (CASCADE)
    - `inscription_id` (NOT nullable) → **Inscription** (CASCADE)
- **Cascade**: YES on both sequence and inscription deletion
- **Cardinality Analysis**:
    - Sequence **(1,N)** ↔ Appreciation **(0,N)** - One sequence, many student appreciations
    - Inscription **(1,N)** ↔ Appreciation **(0,N)** - One student enrollment, many appreciations per sequence
- **Is This an Entity?**: **WEAK ENTITY** - Qualitative assessment records

---

### Association 8: **Moyenne** (Subject Average per Student)

- **Table**: `moyennes`
- **Purpose**: Stores calculated averages for a student per subject, optionally per sequence or trimester
- **Business Attributes**:
    - `valeur` (decimal 4,2) - Average score out of 20
    - `coefficient` (integer) - Coefficient applied at calculation time
    - `total_points` (decimal 6,2) - valeur × coefficient
    - `rang` (integer) - Student's rank for this subject in the class
    - `moyenne_classe` (nullable decimal 4,2) - Class average for the subject
    - `min_classe` (nullable decimal 4,2) - Lowest score in class
    - `max_classe` (nullable decimal 4,2) - Highest score in class
    - `appreciation` (nullable string) - e.g., "Excellent", "Peut mieux faire"
- **Foreign Keys**:
    - `inscription_id` (NOT nullable) → **Inscription** (CASCADE)
    - `matiere_id` (NOT nullable) → **Matiere** (CONSTRAIN)
    - `sequence_id` (nullable, CASCADE) → **Sequence**
    - `trimestre_id` (nullable, CASCADE) → **Trimestre**
- **Cascade**: YES on inscription, sequence, trimestre deletion
- **Cardinality Analysis**:
    - Inscription **(1,N)** ↔ Moyenne **(0,N)** - One enrollment, averages per subject/period
    - Matiere **(1,N)** ↔ Moyenne **(0,N)** - One subject, averages across students
    - Sequence **(0,N)** ↔ Moyenne **(0,N)** - Sequence-level averages (optional)
    - Trimestre **(0,N)** ↔ Moyenne **(0,N)** - Trimester-level averages (optional)
- **Is This an Entity?**: **WEAK ENTITY** - Calculated/derived but stored for reporting

---

### Association 9: **Bilan** (Academic Report/Summary)

- **Table**: `bilans`
- **Purpose**: Summary report of academic performance per student for a sequence/trimester
- **Business Attributes**:
    - `moyenne` (decimal 4,2) - Overall average
    - `rang` (integer) - Student's rank in class
    - `mention` (nullable string) - Distinction (e.g., "Excellent", "Excellent", "Bien")
    - `observation_conseil` (nullable text) - Teacher council observations
- **Foreign Keys**:
    - `inscription_id` (NOT nullable) → **Inscription** (CONSTRAIN)
    - `sequence_id` (nullable, default null) → **Sequence** (CONSTRAIN)
    - `trimestre_id` (nullable, default null) → **Trimestre** (CONSTRAIN)
- **Logic**: AT LEAST ONE of sequence_id or trimestre_id must be set
- **Cardinality Analysis**:
    - Inscription **(1,N)** ↔ Bilan **(0,N)** - One enrollment, multiple reports per sequence/trimester
    - Sequence **(0,N)** ↔ Bilan **(0,N)** - Sequence-level reports (optional)
    - Trimestre **(0,N)** ↔ Bilan **(0,N)** - Trimester-level reports (optional)
- **Is This an Entity?**: **WEAK ENTITY/REPORT** - Aggregated academic summary

---

### Association 10: **Absence** (Attendance Tracking)

- **Table**: `absences`
- **Purpose**: Records absence hours for a student in a sequence
- **Business Attributes**:
    - `heures_justifiees` (integer, DEFAULT: 0) - Justified absence hours
    - `heures_non_justifiees` (integer, DEFAULT: 0) - Unjustified absence hours
- **Foreign Keys**:
    - `sequence_id` (NOT nullable) → **Sequence** (CASCADE)
    - `inscription_id` (NOT nullable) → **Inscription** (CONSTRAIN)
- **Cascade**: YES on sequence deletion
- **Cardinality Analysis**:
    - Sequence **(1,N)** ↔ Absence **(0,N)** - One sequence, many student absence records
    - Inscription **(1,N)** ↔ Absence **(0,N)** - One enrollment, absence per sequence
- **Is This an Entity?**: **WEAK ENTITY** - Attendance measure records

---

## SECTION 3: COMPLETE CARDINALITY SPECIFICATION (UML Format)

### Hierarchy Chain

```
Cycle (1,1) ←→ (1,N) Niveau (1,1) ←→ (1,N) Classe
AnneeScolaire (1,1) ←→ (1,N) Classe
Classe (1,1) ←→ (0,N) Salle
Classe (1,1) ←→ (1,N) Inscription
AnneeScolaire (1,1) ←→ (1,N) Trimestre
Trimestre (1,1) ←→ (1,N) Sequence
```

### User & Teacher Hierarchy

```
User (0,1) ←—→ (1,1) Enseignant [hasOne]
Enseignant (0,N) ←→ (1,1) Departement [optional assignment, SET NULL]
```

### Subject Assignments

```
Matiere (0,N) ←→ (0,N) Classe [via classe_matiere with coefficient, ordre]
Enseignant (0,N) ←→ (1,N) Affectation
Matiere (1,N) ←→ (0,N) Affectation
Classe (1,N) ←→ (0,N) Affectation
Niveau (1,N) ←→ (0,N) Affectation
AnneeScolaire (1,N) ←→ (0,N) Affectation
→ CONSTRAINT: (matiere_id, classe_id, annee_scolaire_id) UNIQUE
```

### Student Enrollment (Core Academic Link)

```
Eleve (1,N) ←→ (1,1) Inscription
Classe (1,N) ←→ (0,N) Inscription
AnneeScolaire (1,N) ←→ (1,1) Inscription
→ CONSTRAINT: (eleve_id, annee_scolaire_id) UNIQUE
```

### Evaluation & Grading

```
Sequence (1,N) ←→ (0,N) Evaluation
Matiere (1,N) ←→ (0,N) Evaluation
Classe (1,N) ←→ (0,N) Evaluation
Enseignant (1,N) ←→ (0,N) Evaluation

Evaluation (1,N) ←→ (0,N) Note
Inscription (1,N) ←→ (0,N) Note
→ Represents: Student receives grade on specific evaluation
```

### Performance Aggregations

```
Inscription (1,N) ←→ (0,N) Moyenne
Matiere (1,N) ←→ (0,N) Moyenne
Sequence (0,N) ←→ (0,N) Moyenne  [optional, nullable]
Trimestre (0,N) ←→ (0,N) Moyenne  [optional, nullable]

Inscription (1,N) ←→ (0,N) Bilan
Sequence (0,N) ←→ (0,N) Bilan  [optional, nullable]
Trimestre (0,N) ←→ (0,N) Bilan  [optional, nullable]
→ Pattern: Aggregates grades at sequence or trimester level
```

### Behavioral & Attendance

```
Sequence (1,N) ←→ (0,N) Appreciation
Inscription (1,N) ←→ (0,N) Appreciation

Sequence (1,N) ←→ (0,N) Absence
Inscription (1,N) ←→ (0,N) Absence
```

---

## SECTION 4: CARDINALITY LEGEND

| Notation | Meaning                      | Example                                        |
| -------- | ---------------------------- | ---------------------------------------------- |
| (1,1)    | ONE and ONLY ONE (mandatory) | Each Enseignant MUST have exactly one User     |
| (0,1)    | ZERO or ONE (optional)       | A User MAY have zero or one Enseignant record  |
| (1,N)    | ONE to MANY                  | One AnneeScolaire has MANY Trimestres          |
| (0,N)    | ZERO to MANY (optional)      | One Sequence MAY have zero to many Evaluations |

### Reading Direction

- **Left side**: Cardinality from left entity's perspective
- **Right side**: Cardinality from right entity's perspective
- Example: `User (0,1) ←→ (1,1) Enseignant`
    - From User: a user may have 0 or 1 enseignant record
    - From Enseignant: an enseignant must have 1 user

---

## SECTION 5: NULLABLE FOREIGN KEYS (Determining Optionality)

| Table       | Column          | Nullable     | Target      | Behavior  | Cardinality Impact                         |
| ----------- | --------------- | ------------ | ----------- | --------- | ------------------------------------------ |
| enseignants | departement_id  | YES          | Departement | SET NULL  | Enseignant (0,N) → Departement             |
| classes     | salle_id        | YES          | Salle       | SET NULL  | Classe (0,1) → Salle                       |
| bilans      | sequence_id     | YES          | Sequence    | CONSTRAIN | Bilan can be sequence OR trimester level   |
| bilans      | trimestre_id    | YES          | Trimestre   | CONSTRAIN | Bilan can be sequence OR trimester level   |
| moyennes    | sequence_id     | YES          | Sequence    | CASCADE   | Moyenne can be sequence OR trimester level |
| moyennes    | trimestre_id    | YES          | Trimestre   | CASCADE   | Moyenne can be sequence OR trimester level |
| notes       | est_validee     | NO (boolean) | N/A         | N/A       | Grade validation status is mandatory       |
| evaluations | date_evaluation | YES          | N/A         | N/A       | Evaluation date is optional                |

---

## SECTION 6: CASCADE DELETE ANALYSIS (Data Flow on Deletions)

### Level 1: Academic Year (CRITICAL)

```
DELETE AnneeScolaire
  ↓ CASCADE
  - Deletes ALL Trimestres
  - Deletes ALL Classes
  - Deletes ALL Inscriptions
  - Deletes ALL Affectations
```

### Level 2: Cycle/Niveau (CRITICAL)

```
DELETE Cycle
  ↓ CASCADE
  - Deletes Niveaux
    ↓ CASCADE
    - Deletes Classes (if no other year)
```

### Level 3: Trimestre/Sequence (CRITICAL)

```
DELETE Trimestre
  ↓ CASCADE
  - Deletes Sequences
    ↓ CASCADE
    - Deletes Evaluations
    - Deletes Appreciations
    - Deletes Moyennes (trimestre level)
    - Deletes Absences
  - Deletes Bilans (trimestre level)
  - Deletes Moyennes (trimestre level)
```

### Level 4: Other Cascades

```
DELETE Classe → CASCADE Salles, (Inscriptions via year), Affectations
DELETE Matiere → NO CASCADE (Set Null in evaluations, affectations)
DELETE Enseignant → NO CASCADE (Set Null in affectations, evaluations)
DELETE Salle → NO CASCADE though defined with CASCADE
DELETE Sequence → CASCADE Evaluations, Notes, Appreciations, Moyennes, Absences
DELETE Evaluation → CASCADE Notes
```

---

## SECTION 7: UNIQUE CONSTRAINTS (Data Uniqueness Rules)

| Table          | Columns                                    | Purpose                                    | Implication                    |
| -------------- | ------------------------------------------ | ------------------------------------------ | ------------------------------ |
| users          | email                                      | One email per system user                  | Authentication uniqueness      |
| eleves         | matricule                                  | One ID per student                         | Student identification         |
| enseignants    | matricule                                  | One ID per teacher                         | Teacher identification         |
| matieres       | code                                       | One code per subject                       | Subject identification         |
| etablissements | code_ecole                                 | One code per school                        | School identification          |
| inscriptions   | (eleve_id, annee_scolaire_id)              | Student can only enroll once per year      | Prevents duplicate enrollments |
| affectations   | (matiere_id, classe_id, annee_scolaire_id) | One teacher per subject per class per year | Prevents duplicate assignments |

---

## SECTION 8: INDIRECT RELATIONSHIPS (Through Associations)

### Example 1: Student → Subject Performance

```
Eleve (1,N) → Inscription (0,N) → Note (0,N) → Evaluation (1,1) → Matiere
Eleve (1,N) → Inscription (0,N) → Moyenne (0,N) → Matiere
```

**Data Flow**: One student has one enrollment per year → receives multiple notes → across evaluations in one subject

### Example 2: Teacher Impact on Student Grades

```
Enseignant (1,1) → Affectation (0,N) → Classe (1,1) ← Inscription (1,N) ← Eleve
Enseignant (1,1) → Evaluation (0,N) → Note (0,N) → Inscription
```

**Data Flow**: Teacher assigned to class → teaches evaluations → students in that class receive grades

### Example 3: Academic Calendar Structure

```
AnneeScolaire (1,N) → Trimestre (1,N) → Sequence (1,N) → Evaluation (0,N) → Note (0,N) → Inscription (1,1) ← Eleve
```

**Data Flow**: Academic year divided into trimesters → sequences → evaluations → student grades

### Example 4: Performance Aggregation

```
Inscription (1,N) ← Eleve
Inscription (1,N) → Note (0,N) → Evaluation → Sequence/Matiere
Inscription (1,N) → Moyenne  [CALCULATED from Notes]
Inscription (1,N) → Bilan    [SUMMARY of Moyennes]
```

**Data Flow**: Notes are detailed grades → Moyennes aggregate per subject → Bilans summarize per period

---

## SECTION 9: SUMMARY TABLE: ENTITY VS ASSOCIATION

| Table Name         | Type            | Own Attributes                                 | Depends On                                | Purpose                     |
| ------------------ | --------------- | ---------------------------------------------- | ----------------------------------------- | --------------------------- |
| User               | ENTITY          | name, email, password, role                    | None                                      | System authentication       |
| Etablissement      | ENTITY          | nom, adresse, telephone, logo                  | None                                      | School master data          |
| AnneeScolaire      | ENTITY          | libelle, date_debut, date_fin, est_active      | None                                      | Academic year context       |
| Cycle              | ENTITY          | nom                                            | None                                      | Educational structure       |
| Niveau             | ENTITY          | nom                                            | Cycle                                     | Grade levels                |
| Classe             | ENTITY          | nom                                            | Niveau, AnneeScolaire                     | Physical/logical classroom  |
| Salle              | ENTITY          | nom                                            | Classe                                    | Classroom sub-division      |
| Matiere            | ENTITY          | nom, code                                      | None                                      | Subject master data         |
| Enseignant         | ENTITY          | matricule, specialite                          | User, Departement                         | Teacher master data         |
| Eleve              | ENTITY          | matricule, nom, prenom, dob, sexe              | None                                      | Student master data         |
| Trimestre          | ENTITY          | nom                                            | AnneeScolaire                             | Academic term               |
| Sequence           | ENTITY          | nom                                            | Trimestre                                 | Division of term            |
| Departement        | ENTITY          | nom, code, description                         | None                                      | Teacher organization        |
| **Inscription**    | **ASSOC/WEAK**  | date, statut, numero_recu                      | Eleve, Classe, Year                       | **Student enrollment**      |
| **Affectation**    | **ASSOCIATION** | None (only timestamps)                         | Enseignant, Matiere, Classe, Niveau, Year | **Teacher assignment**      |
| **Classe_Matiere** | **ASSOC/PIVOT** | coefficient, ordre                             | Classe, Matiere                           | **Curriculum definition**   |
| **Coefficients**   | **ASSOCIATION** | valeur                                         | Matiere, Classe                           | **⚠️ REDUNDANT**            |
| **Evaluation**     | **WEAK ENTITY** | titre, date_evaluation                         | Sequence, Matiere, Classe, Enseignant     | **Assessment definition**   |
| **Note**           | **WEAK ENTITY** | valeur, est_validee                            | Evaluation, Inscription                   | **Individual grade record** |
| **Appreciation**   | **WEAK ENTITY** | travail, discipline, conduite, commentaire     | Sequence, Inscription                     | **Behavioral assessment**   |
| **Moyenne**        | **WEAK ENTITY** | valeur, coefficient, rang, stats, appreciation | Inscription, Matiere, Sequence, Trimestre | **Subject averages**        |
| **Bilan**          | **REPORT/WEAK** | moyenne, rang, mention, observation            | Inscription, Sequence, Trimestre          | **Academic summary**        |
| **Absence**        | **WEAK ENTITY** | heures_justifiees, heures_non_justifiees       | Sequence, Inscription                     | **Attendance tracking**     |

---

## SECTION 10: KEY INSIGHTS & DESIGN OBSERVATIONS

### ✅ Strengths

1. **Clear Academic Hierarchy**: Cycle → Niveau → Classe provides flexible structure
2. **Temporal Dimension**: AnneeScolaire enables historical data tracking
3. **Granular Evaluation**: Trimestre → Sequence → Evaluation allows detailed assessment
4. **Weak Entity Pattern**: Inscription, Note, Moyenne properly model dependent data
5. **Cascade Strategy**: Well-defined cascades maintain referential integrity

### ⚠️ Issues & Concerns

1. **Redundant Table**: `coefficients` table duplicates `classe_matiere.coefficient`
2. **Salle Usage**: The `salle_id` in `classes` is nullable and potentially confusing (Physical vs logical classroom)
3. **Ambiguous Evaluation**: Is `evaluations.classe_id` the class taking the test or the class for which subject is taught?
4. **Undefined Foreign Key**: `Affectation.niveau_id` appears unrelated to the business logic
5. **No Enseignant in Inscription**: Can't directly know which teacher is responsible for a student's enrollment

### 🔄 Data Flow Patterns

**Grade Input Flow**:

```
Evaluation (created)
  → Note (entered for each student)
    → Moyenne (calculated per subject)
      → Bilan (aggregated per period)
```

**Affectation Flow**:

```
AnneeScolaire + Classe + Matiere
  → Find Affectation
    → Enseignant teaching
      → Creates Evaluations
        → Students submit Grades (Notes)
```

---

## SECTION 11: CARDINALITY SUMMARY TABLE

| Relationship            | Left Entity   | Cardinality | Right Entity | Cardinality |
| ----------------------- | ------------- | ----------- | ------------ | ----------- |
| Enrollment              | Eleve         | 1,N         | Inscription  | 1,1         |
| Enrollment              | Classe        | 1,N         | Inscription  | 0,N         |
| Enrollment              | AnneeScolaire | 1,N         | Inscription  | 1,1         |
| Teacher                 | User          | 0,1         | Enseignant   | 1,1         |
| Dept Assignment         | Departement   | 1,N         | Enseignant   | 0,N         |
| Grade Hierarchy         | Cycle         | 1,N         | Niveau       | 1,1         |
| Class Hierarchy         | Niveau        | 1,N         | Classe       | 1,1         |
| Year Structure          | AnneeScolaire | 1,N         | Classe       | 1,1         |
| Room Assignment         | Classe        | 1,N         | Salle        | 0,N         |
| Subject Curriculum      | Classe        | 0,N         | Matiere      | 0,N         |
| Teacher Assignment      | Enseignant    | 1,N         | Affectation  | 0,N         |
| Subject Assignment      | Matiere       | 1,N         | Affectation  | 0,N         |
| Class Assignment        | Classe        | 1,N         | Affectation  | 0,N         |
| Year Assignment         | AnneeScolaire | 1,N         | Affectation  | 0,N         |
| Term Structure          | AnneeScolaire | 1,N         | Trimestre    | 1,1         |
| Sequence Structure      | Trimestre     | 1,N         | Sequence     | 1,1         |
| Assessment Events       | Sequence      | 1,N         | Evaluation   | 0,N         |
| Grading                 | Evaluation    | 1,N         | Note         | 0,N         |
| Student Grading         | Inscription   | 1,N         | Note         | 0,N         |
| Performance Aggregation | Inscription   | 1,N         | Moyenne      | 0,N         |
| Subject Performance     | Matiere       | 1,N         | Moyenne      | 0,N         |
| Behavioral Assessment   | Inscription   | 1,N         | Appreciation | 0,N         |
| Attendance              | Inscription   | 1,N         | Absence      | 0,N         |
| Academic Reports        | Inscription   | 1,N         | Bilan        | 0,N         |

---

## CONCLUSION

This is a **well-structured educational management system** with:

- **13 Core Entities** (independent master data)
- **10 Association Tables** (relationships with business meaning)
- **Proper use of weak entities** for dependent data (Inscription, Note, Moyenne, Bilan)
- **Clear academic hierarchy** (Year → Term → Sequence → Evaluation → Grade)
- **Student-centric data model** centered on Inscription as the key enrollment entity
- **Historical tracking** through AnneeScolaire enabling year-over-year analysis

**Recommendation**: Remove the redundant `coefficients` table and consolidate with `classe_matiere`.
